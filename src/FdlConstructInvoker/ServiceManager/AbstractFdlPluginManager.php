<?php
namespace FdlConstructInvoker\ServiceManager;

use Zend\ServiceManager\AbstractPluginManager;

abstract class AbstractFdlPluginManager extends AbstractPluginManager
{
    /**
     * The invokable class
     * @var string
     */
    protected $invokable;

    /**
     * We override the createFromInvokable to use our own
     *
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

        $this->invokable = $invokable;

        return $this;
    }

    /**
     * We invoke our object here using eval
     *
     * @param void
     * @return null
     */
    public function construct()
    {
        $args = func_get_args();

        // convert to string
        foreach ($args as $key => $arg) {
            $evalArgs["arg{$key}"] = $arg;
        }
        unset($args);
        extract($evalArgs);

        // we can only use eval and hack our way to it :[
        $args = '$' . implode(', $', array_keys($evalArgs));
        eval("\$instance = new \$this->invokable($args);");

        return $instance;
    }
}
