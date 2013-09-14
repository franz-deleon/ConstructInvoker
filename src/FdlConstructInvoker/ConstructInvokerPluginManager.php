<?php
namespace FdlConstructInvoker;

class ConstructInvokerPluginManager extends ServiceManager\AbstractFdlPluginManager
{
    /**
     * Validates the invokable
     * @see Zend\ServiceManager.AbstractPluginManager::validatePlugin()
     */
    public function validatePlugin($plugin)
    {
        // already validated in AbstractFdlPluginManager so do nothing
    }
}
