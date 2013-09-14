FdlConstructInvoker
===============

This is a ZF2 module to enable passing of arguments to constructors using invokable classes.

INSTALLATION:
-------------

Register FdlConstructInvoker module in application.confic.php  
It is __important__ to note that you need to have the module loaded first __BEFORE__ other modules who uses it!

        return array(
          'modules' => array(
              'FdlConstructInvoker', // <<---- Needs to be ontop of Application module
              'Application',
              ),
          'module_listener_options' => array(
              'module_paths' => array(
                  './module',
                  './vendor'
              ),
              'config_glob_paths' => array('config/autoload/{,*.}{global,local}.php')
          )
        );


USAGE:
------

An example class that you want to register to the service manager.  
Notice the class requires a $brick instance in its constructor.

        // MyClass.php
        namespace class\namespace;
        class Brick
        {
            protected $brick;
            
            public function __construct($brick)
            {
                $this->brick = $brick;
            }
            
            public function getBrick()
            {
                return $this->brick;
            }
            
            public function setBrick($briok)
            {
                $this->brick = $brick;
            }
        }

Register your invokable class in your modules using the provided __getConstructInvokerConfig()__ method.

        // Module.php of Application module
        public function getConstructInvokerConfig()
        {
            return array(
                'invokables' => array(
                    'brick' => 'class\namespace\Brick',
                ),
            );
        }

Now you can access the class anywhere you have the main ServiceManager. For example, inside a controller.
Take note of the __get('brick')->construct()__ method whick accepts arguments for the constructor.

        // from an action controller
        public function SomeAction()
        {
            // access it directly from the ServiceManager
            $brick = $this->getServiceLocator()->get('brick')->construct('Concrete Brick');
            echo $brick->getBrick();
            // returns "Concrete Brick"
            
            // you can also access through the Construct Invoker plugin manager
            $brick2 = $this->getServiceLocator()
                           ->get('getConstructInvokerPlugin')
                           ->get('brick')
                           ->construct('Hollow Blocks');
            echo $brick2->getBrick();
            // creates new instance of brick object and returns "Hollow Blocks"
            
            // same instance of brick
            echo $brick->getBrick(); // returns "Concrete Brick"
            $brick->setBrick('Marbles');
            echo $brick->getBrick() // returns "Marbles"
        }
        
You can also use it for regular invokable classes that are not using constructors

        // pretend SomeClass is registered using getConstructInvokerConfig()
        echo $this->getServiceConfig->get('SomeClass')->someMethod(); // whatever somemethod does...
        
UNDER THE HOOD
--------------

FdlConstructInvoker module uses the Peering Service Manager functionality of the Service Manager.
Modules who disables this may not be able to do directy:

    $this->getServiceLocator()->get('someclass')->construct()
