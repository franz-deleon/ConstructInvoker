<?php
namespace FdlConstructInvoker;

use Zend\ServiceManager\ServiceManager;

use FdlConstructInvoker\ServiceManager\FdlServiceManager;

class Module
{
    public function init($moduleManager)
    {
        $listener = $moduleManager->getEvent()
                                  ->getParam('ServiceManager')
                                  ->get('ServiceListener');

        $listener->addServiceManager(
            'constructInvokerPlugin',
            'construct_invoker_config',
            'FdlConstructInvoker\ConstructInvokerPluginProviderInterface',
            'getConstructInvokerConfig'
        );
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'constructInvokerPlugin' => 'FdlConstructInvoker\ConstructInvokerPluginFactory',
            ),
        );
    }
}
