<?php
require_once __DIR__ . '/../init.php';

error_log(json_encode($_POST));

if(!isset($_POST['token'])){
    return;
}

\Stripe\Stripe::setApiKey(getenv('STRIPE_KEY'));
$sendgrid = new SendGrid(getenv('SENDGRID_KEY'));
$orch = new \andrefelipe\Orchestrate\Client();
$lvtech  = new \andrefelipe\Orchestrate\Application();

$users = $lvtech->collection('users');
$user = $users->item($_POST['stripe_email']);

if(!$user->get()){
    $user['email'] = $_POST['stripe_email'];
    $user['created'] = new DateTime();
    if(!$user->put()){
        throw new RuntimeException('could not persist user');
    }
}

if(!$user['stripe']){ //create a new stripe customer
    try{
        $customer = \Stripe\Customer::create(array(
            "metadata" => [
                "venture" => true,
                "created" => 'venture',
            ],
            "source" => $_POST['token'], // obtained with Stripe.js
            "email" => $_POST['stripe_email'],
        ));

        error_log('created customer for: ' . $_POST['email']);
        error_log('customer id: ' . $customer['id']);

        $user['stripe'] = $customer['id'];

        $user->event('log')->post(['description' => 'stripe customer creation', 'error' => false, 'id' => $customer['id']]);

        if(!$user->put()){
            throw new RuntimeException('could not save stripe information');
        }

    } catch (Exception $e) {
        $user->event('log')->post(['description' => 'stripe customer creation', 'error' => true, 'message' => $e->getMessage()]);
        throw new RuntimeException('could not make charge');
    }
}

if("3500" == $_POST['amount'] OR "2000" == $_POST['amount']){
    $type = 'student';
    $amount = 3500;

    if('lehigh.edu' == substr(strrchr($_POST['email'], "@"), 1)){
        $amount = $amount - 1500;
    }
} elseif(time() < strtotime('10/1/15')) {
    $type = 'early';
    $amount = 4000;
} elseif(time() < strtotime('10/23/15')){
    $type = 'standard';
    $amount = 5000;
} else {
    $type = 'late';
    $amount = 7500;
}


try {
    $charge = \Stripe\Charge::create(array(
        "amount" => $amount,
        "currency" => "USD",
        "customer" => $user['stripe'],
        "description" => "Venture Ticket: " . ucfirst($type)
    ));
    $user->event('log')->post(['description' => 'venture ticket purchase', 'error' => false, 'id' => $charge['id']]);
} catch (Exception $e) {
    $user->event('log')->post(['description' => 'venture ticket purchase', 'error' => true, 'message' => $e->getMessage()]);
    error_log($e->getMessage());
    throw new RuntimeException('could not make charge');
}

error_log('charge id: ' . $charge['id']);

$tickets = $lvtech->collection('tickets');

$ticket = $tickets->item();
$ticket['event'] = 'venture';
$ticket['created'] = new DateTime();
$ticket['edition'] = '2015';
$ticket['stripe_charge'] = $charge['id'];
$ticket['type'] = $type;
$ticket['name'] = $_POST['name'];
$ticket['phone'] = $_POST['phone'];
$ticket['email'] = $_POST['email'];

if(!$ticket->post() OR
   !$ticket->relation('buyer', $user)->put() OR
   !$user->relation('purchase', $ticket)->put()){
    throw new RuntimeException('could not save ticket information');
}

try{
    $email = new \SendGrid\Email();
    $email->addTo($ticket['email'])
        ->addCc($user['email'])
        ->setFrom('tim@lehighvalleytech.org')
        ->setSubject('LVTech: Venture Ticket Confirmation')
        ->setText("Thanks for your order, consider this  email your ticket. See you on Friday the 23rd at Ben Franklin Tech Ventures: http://www.meetup.com/lvtech/events/225087827/");
    $sendgrid->send($email);
    $ticket->event('log')->post(['description' => 'purchase confirmation email', 'error' => false]);
} catch (Exception $e) {
    $ticket->event('log')->post(['description' => 'purchase confirmation email', 'error' => true, 'message' => $e->getMessage()]);
}