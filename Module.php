<?php
namespace FdlConstructInvoker;

use Zend\ModuleManager\ModuleEvent;

class Module
{
    protected $pluginParams = array(
    	'service_manager' => 'constructInvokerPlugin',
        'config_key'      => 'construct_invoker_config',
        'interface'       => 'FdlConstructInvoker\ConstructInvokerPluginProviderInterface',
        'method'          => 'getConstructInvokerConfig'
    );

    protected $moduleManager;
    protected $loadedModules;

    public function init($moduleManager)
    {
        $this->moduleManager = $moduleManager;
        $this->loadedModules = $moduleManager->getLoadedModules();

        $this->addServicePlugin($moduleManager);

        $moduleManager->getEventManager()->attach(
            ModuleEvent::EVENT_LOAD_MODULES_POST,
            array($this, 'reloadModule'),
            1000
        );
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

    public function reloadModule(ModuleEvent $e)
    {
        foreach ($this->loadedModules as $moduleName => $module) {
            $constructConfigExist = false;
            if (method_exists($module, 'getConfig')) {
                $config = $module->getConfig();
                if (!empty($config[$this->pluginParams['config_key']])) {
                    $constructConfigExist = true;
                }
            }

            if (!method_exists($module, $this->pluginParams['method'])
                && !$constructConfigExist
            ) {
                continue;
            }

            // the service plugin is already loaded. No need to reload
            if (__NAMESPACE__ == $moduleName) {
                break;
            }

            $this->moduleManager->getEventManager()->trigger(
                ModuleEvent::EVENT_LOAD_MODULE,
                $this->moduleManager,
                $e->setModuleName($moduleName)->setModule($module)
            );
        }
    }

    protected function addServicePlugin($moduleManager)
    {
        $listener = $moduleManager->getEvent()
                                  ->getParam('ServiceManager')
                                  ->get('ServiceListener');

        $listener->addServiceManager(
            $this->pluginParams['service_manager'],
            $this->pluginParams['config_key'],
            $this->pluginParams['interface'],
            $this->pluginParams['method']
        );
    }
}
