<?php
namespace Event\Factory;

use andrefelipe\Orchestrate\Application;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class Orchestrate implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return \andrefelipe\Orchestrate\Application
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('config');
        $app = new Application($config['services']['orchestrate']['key']);
        return $app;
    }

}