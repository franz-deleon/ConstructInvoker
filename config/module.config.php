<?php
return array(
    'service_manager' => array(
        'factories' => array(
            'constructInvokerPlugin' => 'FdlConstructInvoker\ConstructInvokerPluginFactory',
        ),
    ),
    'service_listener_options' => array(
        'service_manager' => 'constructInvokerPlugin',
        'config_key'      => 'construct_invoker_config',
        'interface'       => 'FdlConstructInvoker\ConstructInvokerPluginProviderInterface',
        'method'          => 'getConstructInvokerConfig',
    ),
    'module_reloader' => array(
        array(
            'name' => '*',
            'callback' => function ($moduleInstance, $serviceManager) {
                $constructConfigExist = false;
                $config = $serviceManager->get('config');
                $serviceListenerOptions = $config['service_listener_options'];

                if (method_exists($moduleInstance, 'getConfig')) {
                    $moduleConfig = $moduleInstance->getConfig();
                    if (!empty($moduleConfig[$serviceListenerOptions['config_key']])) {
                        $constructConfigExist = true;
                    }
                }

                if (method_exists($moduleInstance, $serviceListenerOptions['method'])
                    || $constructConfigExist
                ) {
                    return true;
                }

                return false;
            },
        )
    ),
);
