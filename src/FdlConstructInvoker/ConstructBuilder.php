<?php
namespace FdlConstructInvoker;

use Zend\ServiceManager;

class ConstructBuilder
{
    /**
     * Name of the invokable class
     * @var string
     */
    protected $targetClass;

    /**
     * @var ServiceManager\ServiceManager
     */
    protected $pluginServiceManager;

    /**
     * @var boolean
     */
    protected $createFromPluginServiceManager = false;

    /**
     * Reconstruct the object when invoked
     * @return object Invokable object
     */
    public function __invoke()
    {
        $args = func_get_args();
        return call_user_func_array(array($this, 'construct'), $args);
    }

    /**
     * Forward method calls to our invoked object
     * @param string $name
     * @param string $args
     */
    public function __call($name, $args)
    {
        if (is_object($this->targetClass)) {
            $callable = array($this->targetClass, $name);
            if (is_callable($callable)) {
                return call_user_func_array($callable, $args);
            }
        } else {
            return call_user_func_array(array($this->construct(), $name), $args);
        }
    }

    /**
     * The target class to initialize
     * @param unknown $targetClass
     * @return \FdlConstructInvoker\ConstructBuilder
     */
    public function setTargetClass($targetClass)
    {
        $this->targetClass = $targetClass;
        return $this;
    }

    /**
     * Set the creatioon options overriding it.
     * @param array $creationOptions
     * @return \FdlConstructInvoker\ConstructBuilder
     */
    public function setCreationOptions(array $creationOptions)
    {
        $this->creationOptions = $creationOptions;
        return $this;
    }

    /**
     * Recycle the plugin service manager
     * @param ServiceManager\ServiceManager $serviceManager
     * @return \FdlConstructInvoker\ConstructBuilder
     */
    public function setPluginServiceManager(ServiceManager\ServiceManager $serviceManager)
    {
        $this->pluginServiceManager = $serviceManager;
        return $this;
    }

    /**
     * Is the tartgetClass being initialized from the PluginManager?
     * @param boolean $flag
     * @return \FdlConstructInvoker\ConstructBuilder
     */
    public function setCreateFromPluginServiceManager($flag)
    {
        $this->createFromPluginServiceManager = (bool) $flag;
        return $this;
    }

    /**
     * Intantiate the invokable class string
     * @param void
     * @return object
     */
    public function construct()
    {
        if (!empty($this->creationOptions)) {
            $args = $this->creationOptions;
        } else {
            $args = func_get_args();
        }

        if ($this->createFromPluginServiceManager) {
            if (!$this->pluginServiceManager) {
                throw new Exception\ErrorException('The plugin service manager is missing');
            } else {
                $name = uniqid('cb-');
                $this->setCreateFromPluginServiceManager(false);

                // do the instantiation from the plugin service manager
                $this->pluginServiceManager->setInvokableClass($name, $this->targetClass);
                $this->targetClass = $this->pluginServiceManager->get($name, $args);
            }
        } else {
            if (!empty($args)) {
                // convert to string
                $evalArgs = array();
                foreach ($args as $key => $arg) {
                    $evalArgs["arg{$key}"] = $arg;
                }
                unset($args);
                extract($evalArgs);

                // we can only use eval and hack our way to it :[
                $evalArgs = '$' . implode(', $', array_keys($evalArgs));
                eval("\$this->targetClass = new \$this->targetClass($evalArgs);");
                unset($evalArgs);
            } else {
                $this->targetClass = new $this->targetClass();
            }
        }

        return $this->targetClass;
    }
}
