<?php
namespace FdlConstructInvoker;

use Zend\ServiceManager\ServiceManager;
use Zend\ModuleManager\ModuleEvent;
use FdlConstructInvoker\ServiceManager\FdlServiceManager;

class Module
{
    public function init($moduleManager)
    {
        $moduleEvent = $moduleManager->getEvent();
        $loadedModules = $moduleManager->getLoadedModules();
        $eventManager = $moduleManager->getEventManager();
        $listener = $moduleEvent->getParam('ServiceManager')->get('ServiceListener');

        $listener->addServiceManager(
            'constructInvokerPlugin',
            'construct_invoker_config',
            __NAMESPACE__ . '\ConstructInvokerPluginProviderInterface',
            'getConstructInvokerConfig'
        );

        $eventManager->attach(ModuleEvent::EVENT_LOAD_MODULES_POST, function ($e) use ($loadedModules, $listener, $moduleEvent) {
            foreach ($loadedModules as $moduleName => $module) {
                if (!method_exists($module, 'getConstructInvokerConfig')) {
                    continue;
                }

                if (__NAMESPACE__ == $moduleName) {
                    break;
                }

                $listener->onLoadModule($moduleEvent);
            }
        });


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
}
