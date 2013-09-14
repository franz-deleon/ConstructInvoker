<?php
namespace FdlConstructInvoker\ServiceManager;

use FdlConstructInvoker\ConstructBuilder;

use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\Exception;

abstract class AbstractFdlPluginManager extends AbstractPluginManager
{
    /**
     * We override the createFromInvokable method to use our own
     * and delegate the job to builder constructor.
     *
     * @override
     * @param  string $canonicalName
     * @param  string $requestedName
     * @return null|\stdClass
     * @throws Exception\ServiceNotFoundException If resolved class does not exist
     */
    protected function createFromInvokable($canonicalName, $requestedName)
    {
        $invokable = $this->invokableClasses[$canonicalName];
        if (!class_exists($invokable)) {
            throw new Exception\ServiceNotFoundException(sprintf(
                '%s: failed retrieving "%s%s" via invokable class "%s"; class does not exist',
                get_class($this) . '::' . __FUNCTION__,
                $canonicalName,
                ($requestedName ? '(alias: ' . $requestedName . ')' : ''),
                $invokable
            ));
        }

        // Delegate the instantiation of the invokable
        // This prevents recursion of the service manager
        return new ConstructBuilder($invokable);
    }
}
