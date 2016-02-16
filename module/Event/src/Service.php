<?php
namespace Event;
use andrefelipe\Orchestrate\Application;

class Service
{
    protected $application;
    protected $sendgrid;

    public function __construct(Application $application, \SendGrid $sendgrid)
    {
        $this->application = $application;
        $this->sendgrid = $sendgrid;
    }

    public function getEvent($id)
    {
        $events = $this->application->collection('events');
        $event = $events->item($id);

        if(!$event->get()){
            throw new \Exception('invalid event: ' . $id);
        }

        return $event;
    }

    public function getEventInfo($event)
    {
        $info = $event->toArray();
        foreach($info['value']['tickets'] as $id => $data){
            if(!isset($data['until'])) continue;
            $info['value']['tickets'][$id]['until'] = new \DateTime($data['until']['date'], new \DateTimeZone($data['until']['timezone']));
        }
        return $info['value'];
    }

    public function getBestTicket($event, $email, $code = '')
    {
        $info = $this->getEventInfo($event);
        $possible = $info['tickets'];
        $now = new \DateTime();
        foreach($possible as $type => $data)
        {
            if(isset($data['until']) AND $now > $data['until']){
                unset($possible[$type]);
            }

            if(isset($data['code']) AND $code !== $data['code']){
                unset($possible[$type]);
            }

            if(isset($data['email']) AND !preg_match($data['email'], $email)){
                unset($possible[$type]);
            }
        }

        return array_reduce($possible, function($last, $current){
            if(empty($last)){
                return $current;
            }

            if($last['amount'] > $current['amount']){
                return $current;
            }

            return $last;
        });
    }

    public function createTicket($event, $name, $phone, $email, $code = null)
    {
        $ticketData = $this->getBestTicket($event, $email, $code);

        $tickets = $this->application->collection('tickets');

        $ticket = $tickets->item();
        $ticket['event'] = $event['id'];
        $ticket['edition'] = $event['edition'];
        $ticket['created'] = new \DateTime();
        $ticket['type'] = $ticketData['title'];
        $ticket['name'] = $name;
        $ticket['phone'] = $phone;
        $ticket['email'] = $email;
        $ticket['amount'] = $ticketData['amount'];

        if(!$ticket->post()){
            throw new \Exception('could not save ticket');
        }

        $ticket->relation('event', $event)->put();
        $event->relation('tickets', $ticket)->put();

        return $ticket;
    }

    public function getUser($email)
    {
        $users = $this->application->collection('users');
        $user = $users->item($email);

        if(!$user->get()){
            error_log('creating user');
            $user['email'] = $email;
            $user['created'] = new \DateTime();
            if(!$user->put()){
                throw new \RuntimeException('could not persist user');
            }
        }

        return $user;
    }

    public function payTicket($ticket, $email, $token)
    {
        $user = $this->getUser($email);

        if(!$user['stripe']){ //create a new stripe customer
            try{
                $customer = \Stripe\Customer::create(array(
                    "metadata" => [
                        $ticket['event'] => true,
                        "created" => $ticket['event'],
                    ],
                    "source" => $token,
                    "email" => $email,
                ));

                error_log('created customer for: ' . $_POST['email']);
                error_log('customer id: ' . $customer['id']);

                $user['stripe'] = $customer['id'];

                $user->event('log')->post(['description' => 'stripe customer creation', 'error' => false, 'id' => $customer['id']]);

                if(!$user->put()){
                    throw new \RuntimeException('could not save stripe information');
                }

            } catch (\Exception $e) {
                $user->event('log')->post(['description' => 'stripe customer creation', 'error' => true, 'message' => $e->getMessage()]);
                throw new \RuntimeException('could not make charge');
            }
        } else {
            $customer = \Stripe\Customer::retrieve($user['stripe']);
            $customer->source = $token;
            $customer->save();
            $user->event('log')->post(['description' => 'stripe customer update', 'error' => false, 'id' => $customer['id']]);
        }

        try {
            $charge = \Stripe\Charge::create(array(
                "amount" => $ticket['amount'],
                "currency" => "USD",
                "customer" => $customer,
                "description" => $ticket['type'] . " Ticket: " . implode(' ', [
                        $ticket['event'],
                        $ticket['edition']
                    ])
            ));
            $user->event('log')->post(['description' => 'venture ticket purchase', 'error' => false, 'id' => $charge['id']]);
        } catch (Exception $e) {
            $user->event('log')->post(['description' => 'venture ticket purchase', 'error' => true, 'message' => $e->getMessage()]);
            error_log($e->getMessage());
            throw new RuntimeException('could not make charge');
        }

        $ticket['stripe_charge'] = $charge['id'];
        if(!$ticket->put() OR
            !$ticket->relation('buyer', $user)->put() OR
            !$user->relation('purchase', $ticket)->put()){
            throw new RuntimeException('could not save ticket information');
        }

        try{
            $email = new \SendGrid\Email();
            $email->addTo($ticket['email'])
                ->addCc($user['email'])
                ->setFrom('tim@lehighvalleytech.org')
                ->setSubject('LVTech: VTicket Confirmation')
                ->setText("Thanks for your order, consider this email your ticket. If you need to reference this order, use code: " . $ticket->getKey());
            $this->sendgrid->send($email);
            $ticket->event('log')->post(['description' => 'purchase confirmation email', 'error' => false]);
        } catch (Exception $e) {
            $ticket->event('log')->post(['description' => 'purchase confirmation email', 'error' => true, 'message' => $e->getMessage()]);
        }
    }

    public function getTicket($id)
    {
        $tickets = $this->application->collection('tickets');
        $ticket = $tickets->item($id);

        if(!$ticket->get()){
            throw new \Exception('invalid ticket: ' . $id);
        }

        return $ticket;
    }

}