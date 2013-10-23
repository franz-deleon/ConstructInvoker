<?php
namespace FdlConstructInvoker;

class Module
{

    public function init($moduleManager)
    {
        $this->addServicePlugin($moduleManager);
    }

    public function getServiceConfig()
    {
        return array(
            'invokables' => array(
                'constructBuilder' => __NAMESPACE__ . '\ConstructBuilder',
            ),
            'shared' =>array(
                'constructBuilder' => false,
            ),
        );
    }

    public function getConfig()
    {
        $config = include __DIR__ . '/config/module.config.php';
        return $config;
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

    protected function addServicePlugin($moduleManager)
    {
        $serviceManager = $moduleManager->getEvent()->getParam('ServiceManager');
        $listener = $serviceManager->get('ServiceListener');
        $config   = $this->getConfig();

        $listener->addServiceManager(
            $config['service_listener_options']['service_manager'],
            $config['service_listener_options']['config_key'],
            $config['service_listener_options']['interface'],
            $config['service_listener_options']['method']
        );
    }
}
