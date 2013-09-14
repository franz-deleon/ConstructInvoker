<?php
namespace FdlConstructInvoker;

class ConstructInvokerPluginManager extends ServiceManager\AbstractFdlPluginManager
{
    public function validatePlugin($plugin)
    {
        if (!method_exists($plugin, '__construct')) {
            throw new \ErrorException('Only accept objects with constructors');
        }
    }
}
