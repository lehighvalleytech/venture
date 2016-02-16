<?php
namespace Event\Factory;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class Service implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $orchestrate = $serviceLocator->get('Orchestrate');
        $sendgrid = $serviceLocator->get('Sendgrid');
        return new \Event\Service($orchestrate, $sendgrid);
    }

}