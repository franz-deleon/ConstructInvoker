FdlConstructInvoker
===============

A ZF2 module that enables argument passing to class constructors (`__construct()`) that
requires or not require arguments at construction time.

Quick glimpse:

    // syntax
    $this->getServiceLocator()->get('MyInvokableClass')->construct([$firstArg [, $secondArg [, $...]]]);


INSTALLATION:
-------------

Register FdlConstructInvoker module in application.config.php. 
It is __important__ to note that you need to have the module loaded first __BEFORE__ other modules who uses it!

        return array(
          'modules' => array(
              'FdlConstructInvoker', // <<---- Needs to be ontop of Application module
              'Application', // I use FdlConstructInvoker so i need to be bellow it!
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
Notice the class requires a `$brick` instance in its constructor.

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

Register your invokable classes in your modules using the provided `getConstructInvokerConfig()` method.

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
Take note of the `get('brick')->construct()` method whick accepts arguments for the constructor.

        // from an action controller
        public function SomeAction()
        {
            // access it directly from the ServiceManager
            $brick = $this->getServiceLocator()->get('brick')->construct('Concrete Brick');
            echo $brick->getBrick();
            // returns "Concrete Brick"
            
            // you can also access through the Construct Invoker plugin manager
            $brick2 = $this->getServiceLocator()
                           ->get('constructInvokerPlugin')
                           ->get('brick')
                           ->construct('Hollow Blocks');
            echo $brick2->getBrick();
            // creates new instance of brick object and returns "Hollow Blocks"
            
            // same instance of brick
            echo $brick->getBrick(); // returns "Concrete Brick"
            $brick->setBrick('Marbles');
            echo $brick->getBrick() // returns "Marbles"
        }
        
It will also work for regular invokable classes that are not using constructors:

        // pretend SomeClass is registered using getConstructInvokerConfig()
        echo $this->getServiceConfig->get('SomeClass')->someMethod(); // whatever somemethod does...
        
UNDER THE HOOD
--------------

FdlConstructInvoker module uses the Peering Service Manager functionality of the Service Manager.
Modules who disable this may not be able to do directy below:

    $this->getServiceLocator()->get('someclass')->construct()

If this is the case. Use the Construct Invoker Plugin Manager:

    $this->getServiceLocator()->get('constructInvokerPlugin')->get('someclass')->construct();
