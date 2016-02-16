<?php
namespace Event;

use Prismic\Api as Prismic;
use Prismic\Document;
use Prismic\Predicates;
use Zend\EventManager\EventManagerInterface;
use Zend\Form\Form;
use Zend\Form\Element;
use Zend\InputFilter\Input;
use Zend\InputFilter\InputFilter;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\MvcEvent;
use Zend\Stdlib\RequestInterface as Request;
use Zend\Stdlib\ResponseInterface as Response;
use Zend\Validator\NotEmpty;
use Zend\View\Model\ViewModel;

class Controller extends AbstractActionController
{
    protected $prismic;
    protected $service;

    public function __construct(Prismic $prismic, Service $service)
    {
        $this->prismic = $prismic;
        $this->service = $service;
    }

    public function setEventManager(EventManagerInterface $events)
    {
        parent::setEventManager($events);

        $controller = $this;
        $events->attach('dispatch', function ($e) use ($controller) {
            if($this->params('event')) {
                $controller->layout('layout/' . $controller->params('event'));
                $event = $controller->prismic->getByUID('event', $controller->params('event'));
                $controller->layout()->setVariable('event', $event);
            }
        }, 100);
    }

    public function indexAction()
    {
        //grab all the event docs
        /* @var $response \Prismic\Response */
        $response = $this->prismic->forms()->everything->query([
            Predicates::at('document.tags', [$this->params('event')])
        ])->ref($this->prismic->master())->submit();

        $event = null;
        $schedule = [];
        $sections  = [];

        //get all landing page data
        /* @var $doc \Prismic\Document */
        do {
            foreach($response->getResults() as $doc){
                switch($doc->getType()){
                    case 'event':
                        $event = $doc;
                        break;
                    case 'schedule':
                    case 'section':
                        $sections[$doc->getId()] = $doc;
                        break;
                }
            }

            if($next = $response->getNextPage()){
                throw new \Exception('can not page');
            }

        } while ($next);

        $sections = $this->orderContent($event, 'event.sections', $sections);
        $schedule = $this->orderContent($event, 'event.schedules', $schedule);

        $view = new ViewModel([
            'event'    => $event,
            'sections' => $sections,
            'schedule' => $schedule
        ]);

        $view->setTemplate($this->params('event') . '/index');
        return $view;
    }

    protected function registerAction()
    {
        $event = $this->service->getEvent($this->params('event'));
        $form = new Form('register');

        $name = new Element\Text('name', [
            'required' => true,
            'allowEmpty' => false,
            'label' => 'Name'
        ]);

        $phone = new Element\Text('phone', [
            'required' => true,
            'allowEmpty' => false,
            'label' => 'Phone'
        ]);

        $email = new Element\Email('email', [
            'required' => true,
            'allowEmpty' => false,
            'label' => 'Email'
        ]);

        $code = new Element\Text('code', [
            'required' => false,
            'label' => 'Code'
        ]);

        $inputFilter = new InputFilter();
        $required = new Input();
        $required->getValidatorChain()->attach(new NotEmpty());
        $inputFilter->add($required, 'name');
        $required = clone $required;
        $inputFilter->add($required, 'phone');

        $form->add($name);
        $form->add($phone);
        $form->add($email);
        $form->add($code);

        $form->setInputFilter($inputFilter);

        if($this->getRequest()->isPost()){
            $form->setData($this->getRequest()->getPost());
            if($form->isValid()){
                $ticket = $this->service->createTicket(
                    $event,
                    $form->getInputFilter()->getValue('name'),
                    $form->getInputFilter()->getValue('phone'),
                    $form->getInputFilter()->getValue('email'),
                    $form->getInputFilter()->getValue('code')
                );

                $id = $ticket->getKey();
                return $this->redirect()->toRoute('event', [
                    'action' => 'payment',
                    'event' => $this->params('event'),
                ], [
                    'query' => [
                        'id' => $id
                    ]
                ]);
            }
        }

        //just show info
        return new ViewModel([
            'event' => $this->service->getEventInfo($event),
            'form' => $form
        ]);
    }

    protected function paymentAction()
    {
        //better ways to do this
        $config = $this->getServiceLocator()->get('config');
        \Stripe\Stripe::setApiKey($config['services']['stripe']['secret']);

        if(!$id = $this->request->getQuery('id')){
            $this->redirect()->toRoute('event', [
                'action' => 'register'
            ]);
        }

        $ticket = $this->service->getTicket($id);

        if($this->request->isPost()){
            $this->service->payTicket($ticket, $this->request->getPost('email'), $this->request->getPost('stripeToken'));
            return $this->redirect()->toRoute('event', [
                'action' => 'ticket',
                'event' => $this->params('event'),
            ], [
                'query' => [
                    'id' => $id
                ]
            ]);
        }


        //just show info
        return new ViewModel([
            'ticket' => $ticket,
            'stripe' => $config['services']['stripe']['public']
        ]);
    }

    protected function ticketAction()
    {
        if(!$id = $this->request->getQuery('id')){
            $this->redirect()->toRoute('event', [
                'action' => 'register'
            ]);
        }

        $ticket = $this->service->getTicket($id);

        return new ViewModel([
            'ticket' => $ticket,
        ]);
    }

    protected function lvtechAction()
    {

    }

    protected function orderContent($event, $group, $fetched)
    {
        $ordered = [];
        if(!$event->getGroup($group)){
            return $ordered;
        }

        foreach($event->getGroup($group)->getArray() as $link){
            $id = $link['link']->getId();
            if(isset($fetched[$id])){
                $ordered[$id] = $fetched[$id];
            } else {
                $ordered[$id] = $this->prismic->getByID($id);
            }
        }

        return $ordered;

    }

}