<?php

/**
 * @package x20
 * @author Joshua L. Johnson <josh@ua1.us>
 * @link http://labs.ua1.us
 * @copyright Copyright 2016, Joshua L. Johnson
 * @license MIT
 */

namespace x20\core;

use Exception;
use ReflectionClass;
use x20\core\x20graph;
use x20\core\x20module;

/**
 * The x20 Class is what makes x20 possible. This class handles the
 * entire Dependency Injection environment.
 */
class x20
{
    /**
     * Holds the instantiated x20 singleton object.
     *
     * @static
     * @var x20
     */
    private static $_x20;

    /**
     * An array that contains all registered module objects.
     *
     * @var array
     */
    private $_modules;
    
    private $_serviceModuleMap;

    /**
     * An array that contains all of the module IDs that were loaded
     * into the runtime execution environment of x20.
     *
     * @var array
     */
    private $_loadedModules;

    /**
     * An array of factory constructors that were registered in x20 modules
     * that were loaded during the runtime execution of x20.
     *
     * @var array
     */
    private $_factoryServices;

    /**
     * An array of instantiated singleton object services that were defined as
     * singleton build types in modules that were loaded during the runtime
     * execution of x20.
     *
     * @var array
     */
    private $_singletonServices;

    /**
     * Contains an object that represents the module dependency tree as a
     * graph.
     *
     * @var x20graph
     */
    private $_moduleDependencyGraph;

    /**
     * Contains an object that represents the service dependency tree as a
     * graph.
     *
     * @var x20graph
     */
    private $_serviceDependencyGraph;

    /**
     * x20 constructor sets up x20 properties to allow you to start to register
     * modules and services.
     */
    private function __construct() {
        $this->_modules = [];
        $this->_serviceMap = [];
        // $this->_loadedModules = [];
        // $this->_factoryServices = [];
        // $this->_singletonServices = [];
        $this->_moduleDependencyGraph = new x20graph();
        $this->_serviceDependencyGraph = new x20graph();
    }

    /**
     * Gets a singleton instance of x20. First checks to see if an instance
     * exists. If not, it will create the instance. Then it will return the
     * one and only one instance of x20\core\x20.
     *
     * @static
     * @return x20
     */
    public static function getInstance() {
        if (!isset(self::$_x20) || !(self::$_x20)) {
            self::$_x20 = new self();
        }

        return self::$_x20;
    }
    
    public function getService($className) {
        
    }
    
    public function isService($className) {
        return ($this->_serviceModuleMap[$className]) ? true : false;
    }

    public function isModule($className) {
        return ($this->_modules[$className]) ? true : false;
    }
    /**
     * This method is used to define modules and register services. If a module
     * does not exist, it will create the new module using the moduleInterface
     * factory and return the instantiated module to you for you to add
     * services.
     *
     * @param string $className The class you want use as module.
     * @return mixed The module object
     * @throws Exception
     */
    public function registerModule($className) {
        if (!class_exists($className)) {
            throw new Exception('Cannot find class "' . $className . '"');
        }
        //if module object doesn't exists attempt to create it first and register dependencies
        if (!isset($this->_modules[$className])) {
            $module = new $className();
            if (!$module instanceof x20module) {
                throw new Exception('"' . $className . '" does not inherit from x20\core\x20module');
            }
            
            //get module dependencies
            $dependencies = $module->modules;
            //add module to dependency graph
            $this->_moduleDependencyGraph->addResource($className);
            //add dependencies to module in graph
            $this->_moduleDependencyGraph->addDependencies($className, $dependencies);
            //set put module into module map
            $this->_modules[$className] = $module;
            
            //register dependencent modules
            foreach ($dependencies as $dependency) {
                $this->registerModule($dependency);
            }
        }
    }
   
    public function start() {
        foreach ($this->_modules as $moduleClassName => $module) {
            //run module dependency check for errors
            $this->_moduleDependencyErrorCheck($moduleClassName);
            //register all services for module
            foreach ($module->services as $serviceClassName => $service) {
                $this->_serviceDependencyGraph->addResource($serviceClassName);
                $this->_serviceDependencyGraph->addDependencies($serviceClassName, $service->dependencies);
                $this->_serviceModuleMap[$serviceClassName] = $moduleClassName;
            }
        }
    }
    
    public function run() {
        
    }
    
    private function _start() {
        foreach ($this->_modules as $moduleClassName => $module) {
            if (method_exists($module, 'start')) {
                $refModule = new ReflectionClass($module);
                $moduleStart = $refModule->getMethod('start');
                $parameters = $moduleStart->getParameters();
                if (!empty($parameters)) {
                    $di = [];
                    foreach ($parameters as $parameter) {
                        $dependency = $parameter->getClass();
                        if ($dependency) {
                            $serviceClassName = $dependency->getName();
                            var_dump($serviceClassName);
                        } else {
                            $error = 'While trying to establish dependencies for method "' . $moduleClassName . '::start", ' . 
                            'x20 has found a parameter that has not hinted a class for parameter "$' . $parameter->getName() . '".';
                            throw new Exception($error);
                        }
                    }
                }
            }
        }
    }
    
    private function _run() {
        foreach ($this->_modules as $moduleClassName => $module) {
            $module->run();
        }
    }
    
    private function _moduleDependencyErrorCheck($className) {
        //check to see if there are errors resolving dependencies
        if ($error = $this->_moduleDependencyGraph->runDependencyCheck($className)) {
            switch ($error->code) {
                case 1:
                    $errorMsg = 'While trying to resolve module "' . $className . '", '
                        . 'x20 found that the module dependency "' . $error->resourceId . '" '
                        . 'could not be found.';
                    throw new Exception($errorMsg);
                    break;
                case 2:
                    $errorMsg = 'While trying to resolve module "' . $className . '", '
                        . 'x20 found that there was a cirular dependency caused by the module '
                        . '"' . $error->resourceId . '".';
                    throw new Exception($errorMsg);
                    break;
            }
        }
        
    }
    
    private function _serviceDependencyErrorCheck($className) {
        // //check to see if there are errors resolving dependencies
        // if ($error = $this->_moduleDependencyGraph->runDependencyCheck($className)) {
        //     switch ($error->code) {
        //         case 1:
        //             $errorMsg = 'While trying to resolve module "' . $className . '", '
        //                 . 'x20 found that the module dependency "' . $error->resourceId . '" '
        //                 . 'could not be found.';
        //             throw new Exception($errorMsg);
        //             break;
        //         case 2:
        //             $errorMsg = 'While trying to resolve module "' . $className . '", '
        //                 . 'x20 found that there was a cirular dependency caused by the module '
        //                 . '"' . $error->resourceId . '".';
        //             throw new Exception($errorMsg);
        //             break;
        //     }
        // }
    }
    
    private function _invokeModuleMethod($className, $methodName) {
        //get dependencies/ for each dependency inject it into di and call method
    }


//=========================================================================

    /**
     * This method is used to force a module to be loaded into the x20 runtime
     * environment.
     *
     * @param string $module_id The unique ID that identifies the module you
     *                          intend to load into the x20 runtime environment
     *
     * @return x20
     */
    public function loadModule($module_id)
    {
        if (!in_array($module_id, $this->loadedModules)) {
            $this->_loadModule($module_id);
        }

        return $this;
    }

    /**
     * This method is used to inject a service anywhere you may need it. The
     * service will be returned as the instantiated service.
     *
     * @param string $service_id The unique ID that identifies the service you
     *                           intend to inject
     *
     * @return mixed The service object you identified by $service_id
     */
    // public function inject($service_id)
    // {
    //     return $this->_invokeService($service_id, true);
    // }

    /**
     * This method determines if a $service_id is a registered service.
     *
     * @param string $service_id A unique ID that identifies the service you
     *                           would like to validate
     *
     * @return bool If the service was registered
     */
    // public function isService($service_id)
    // {
    //     if (isset($this->serviceBlueprints[$service_id])) {
    //         return true;
    //     } else {
    //         return false;
    //     }
    // }

    /**
     * This method is responsible for firing the modules execute services that were registered with
     * the module using the execute() method.
     */
    public function execute()
    {
        foreach ($this->loadedModules as $module_id) {
            if ($this->serviceDependencyGraph->isResource($module_id.'_exec')) {
                $this->invoker($module_id.'_exec');
            }
        }
    }

    /**
     * This method is used to reset the entire instance of x20 so that you
     * can iterate over objects in the same script. Anytime you would need more
     * than one x20 Runtime Environment, you would be required to reset x20 before
     * executing another runtime.
     */
    public function destroy()
    {
        self::$x20 = null;
    }

    /**
     * This method takes a module from being registered with x20 to loading the
     * module's services into the x20 runtime environment.
     *
     * @param string $module_id A unique ID of the module you would like to load
     *
     * @throws Exception
     */
    private function _loadModule($module_id)
    {
        //check to see if there are errors resolving dependencies
        $this->moduleDependencyGraph->runDependencyCheck($module_id);
        $error = $this->moduleDependencyGraph->getDependencyError();
        if (!$error) {
            $dependentModules = $this->moduleDependencyGraph->getDependencyResolveOrder();
            $this->moduleDependencyGraph->resetDepenencyCheck();
            //register all services with dependencyGraph and store service closure
            foreach ($dependentModules as $module_id) {
                if (!in_array($module_id, $this->loadedModules)) {
                    $this->loadedModules[] = $module_id;
                    $services = &$this->modules[$module_id]->services;
                    foreach ($services as $service) {
                        //register all services with the serviceDependencyGraph
                        $this->serviceDependencyGraph->addResource($service->service_id);
                        $this->serviceDependencyGraph->addDependencies($service->service_id, $service->dependencies);
                        //store service in $this->registeredServices
                        $this->factoryServices[$service->service_id] = $service->closure;
                        //store info on how to make the service
                        $this->serviceBlueprints[$service->service_id] = array(
                            'service_type' => $service->service_type,
                            'build_type' => $service->build_type,
                        );
                    }
                    //garbage collect
                    unset($this->modules[$module_id]);
                }
            }
            //run all modules' run services that defined run services.
            foreach ($dependentModules as $module_id) {
                if ($this->serviceDependencyGraph->isResource($module_id.'_run')) {
                    $this->_invokeService($module_id.'_run');
                }
            }
        } else {
            switch ($error['code']) {
                case 1:
                    throw new Exception('Could not find the module "' . $error['resource'] . '".');
                    break;

                case 2:
                    $error = 'While trying to resolve modules\'s dependencies, x20 has encountered a circular ' . 
                    'dependency resource error when resolving the module "' . $error['resource'] . '".';
                    throw new Exception($error);
                    break;
            }
        }
    }

    /**
     * This method is an internal way to invoke a single service and resolve
     * all of its dependencies.
     *
     * @param string $service_id A unique ID for the service you would like to
     *                           invoke.
     * @param bool   $return     A boolean value to determine if you want x20
     *                           to return the invoked service to you or not. Default false.
     *
     * @throws Exception
     */
    private function _invokeService($service_id, $return = false)
    {
        $this->serviceDependencyGraph->runDependencyCheck($service_id);
        $error = $this->serviceDependencyGraph->getDependencyError();
        if ($error) {
            switch ($error['code']) {
                case 1:
                    throw new Exception('Could not find service "'.$error['resource'].'".');
                    break;

                case 2:
                    throw new Exception('While trying to resolve a service\'s dependencies, x20 has encountered a circular dependency resource error when resolving the service "'.$error['resource'].'".');
                    break;
            }
        } else {
            $dependantServices = $this->serviceDependencyGraph->getDependencies($service_id);
            //dependency injection
            $di = array();
            //collect all required dependencies in $di to be injected
            foreach ($dependantServices as $service) {
                $di[] = $this->_resolveService($service);
            }
            //invoke requested service
            if (!$return) {
                $this->_instanciateService($service_id, $di);
            } else {
                return $this->_instanciateService($service_id, $di);
            }
            unset($di);
        }
    }

    /**
     * This method is used to resolve services' dependencies before
     * invoking the service.
     *
     * @param string $service_id A unique ID to identify the service you would
     *                           like to resolve.
     */
    private function _resolveService($service_id)
    {
        $dependencies = $this->serviceDependencyGraph->getDependencies($service_id);
        if (empty($dependencies)) {
            return $this->_instanciateService($service_id, array());
        } else {
            $di = array();
            foreach ($dependencies as $dependency) {
                //set $di[] with all dependencies and invoke
                $di[] = $this->_resolveService($dependency);
            }

            return $this->_instanciateService($service_id, $di);
        }
    }

    /**
     * This method is used to instantiate a service that has all of its
     * dependencies resolved.
     *
     * @param string $service_id           A unique ID of the service you would like to
     *                                     instantiate.
     * @param array  $resolvedDependencies An array that contains all of
     *                                     a services dependencies resolved.
     */
    private function _instanciateService($service_id, $resolvedDependencies)
    {
        $build_type = $this->getServiceBuildType($service_id);
        switch ($build_type) {
            case 'singleton':
                if (!isset($this->singletonServices[$service_id])) {
                    $dependantClosure = $this->factoryServices[$service_id];
                    $this->singletonServices[$service_id] = $dependantClosure->invokeArgs($resolvedDependencies);
                }

                return $this->singletonServices[$service_id];
            default:
                $dependantClosure = $this->factoryServices[$service_id];

                return $dependantClosure->invokeArgs($resolvedDependencies);
        }
    }
}
