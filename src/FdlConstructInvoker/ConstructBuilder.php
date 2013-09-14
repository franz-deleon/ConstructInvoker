<?php
namespace FdlConstructInvoker;

class ConstructBuilder
{
    /**
     * Name of the invokable class
     * @var string
     */
    protected $invokable;

    /**
     * @param string $invokable
     */
    public function __construct($invokable)
    {
        $this->invokable = $invokable;
    }

    /**
     * Forward method calls to our invoked object
     * @param string $name
     * @param string $args
     */
    public function __call($name, $args)
    {
        if (is_object($this->invokable)) {
            $callable = array($this->invokable, $name);
            if (is_callable($callable)) {
                return call_user_func_array($callable, $args);
            }
        } else {
            return call_user_func_array(array($this->construct(), $name), $args);
        }
    }

    /**
     * Intantiate the invokable string
     * @param void
     * @return object
     */
    public function construct()
    {
        $args = func_get_args();

        if (!empty($args)) {
            // convert to string
            $evalArgs = array();
            foreach ($args as $key => $arg) {
                $evalArgs["arg{$key}"] = $arg;
            }
            unset($args);
            extract($evalArgs);

            // we can only use eval and hack our way to it :[
            $args = implode(', $', array_keys($evalArgs));
            $args = (!empty($args)) ? '$' . $args : '';
            eval("\$this->invokable = new \$this->invokable($args);");
        } else {
            $this->invokable = new $this->invokable();
        }

        return $this->invokable;
    }
}
