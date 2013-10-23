<?php
namespace FdlConstructInvoker;

use Zend\Mvc\Service\AbstractPluginManagerFactory;
use Zend\ServiceManager\ServiceLocatorInterface;

class ConstructInvokerPluginFactory extends AbstractPluginManagerFactory
{
    const PLUGIN_MANAGER_CLASS = 'FdlConstructInvoker\ConstructInvokerPluginManager';

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $plugins = parent::createService($serviceLocator);

        // enable this plugin as a peering service manager
        $serviceLocator->addPeeringServiceManager($plugins);
        $serviceLocator->setRetrieveFromPeeringManagerFirst(true);

        return $plugins;
    }
}
