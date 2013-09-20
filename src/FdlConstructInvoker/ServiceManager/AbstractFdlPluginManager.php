<?php
namespace FdlConstructInvoker\ServiceManager;

use FdlConstructInvoker\ConstructBuilder;

use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\Exception;

abstract class AbstractFdlPluginManager extends AbstractPluginManager
{
    /**
     * The target class to initialize
     * @var string
     */
    protected $targetClass;

    /**
     * @var \FdlConstructInvoker\ConstructBuilder
     */
    protected $constructBuilder;

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

        $constructBuilder = $this->getConstructBuilder();
        $constructBuilder->setTargetClass($invokable);

        if (is_array($this->creationOptions) && !empty($this->creationOptions)) {
            $constructBuilder->setCreationOptions($this->creationOptions);
            $instance = $this->construct();
        } else {
            $constructBuilder->setPluginServiceManager($this);
            $constructBuilder->setCreateFromPluginServiceManager(true);
            $instance = $constructBuilder;
        }

        return $instance;
    }

    /**
     * @return \FdlConstructInvoker\ConstructBuilder
     */
    public function getConstructBuilder()
    {
        if (null === $this->constructBuilder) {
            $this->constructBuilder = $this->getServiceLocator()->get('constructBuilder');
        }
        return $this->constructBuilder;
    }

    /**
     * Intantiate the invokable class string
     * @param void
     * @return object
     */
    public function construct()
    {
        $constructBuilder = $this->getConstructBuilder();
        $this->constructBuilder = null;
        return $constructBuilder->construct();
    }
}
