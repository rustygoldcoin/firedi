<?php
/**
 * @package ulfberht
 * @author Joshua L. Johnson <josh@ua1.us>
 * @link http://ua1.us
 * @copyright Copyright 2016, Joshua L. Johnson
 * @license MIT
 */

namespace ulfberht\core;

use Exception;
use ReflectionClass;
use ulfberht\core\graph;
use ulfberht\core\module;
use ulfberht\core\service;

/**
 * The ulfberht class is what makes ulfberht possible. This class handles the
 * entire Dependency Injection environment.
 */
class ulfberht {

    /**
     * @var object The ulfberht instance object.
     */
    private static $_ulfberht;

    /**
     * @var array An array that holds the module objects.
     */
    private $_modules;

    /**
     * @var array A map that provides mappings to services and
     * which module they belong to.
     */
    private $_serviceModuleMap;

    /**
     * @var array Stores singleton object services that have been
     * registered as singleton type service.
     */
    private $_serviceCache;
    
    /**
     * @var \ulfberht\core\graph
     */
    private $_moduleDependencyGraph;
    
    /**
     * @var \ulfberht\core\graph
     */
    private $_serviceDependencyGraph;

    /**
     * @var boolean Identifies if ulfberht has been forged meaning that you
     * may no longer register new services or modules to it.
     */
    private $_forged;

    /**
     * @var array An array of hooks we want to run during the forge process.
     */
    private $_hooks;

    /**
     * The constructor.
     */
    private function __construct() {
        $this->_modules = [];
        $this->_serviceModuleMap = [];
        $this->_serviceCache = [];
        $this->_moduleDependencyGraph = new graph();
        $this->_serviceDependencyGraph = new graph();
        $this->_forged = false;
        $this->_hooks = [];
    }

    /**
     * Gets the singleton instance of this class.
     *
     * @return object The class instance.
     */
    public static function getInstance() {
        if (!isset(self::$_ulfberht) || !(self::$_ulfberht)) {
            self::$_ulfberht = new self();
        }
        return self::$_ulfberht;
    }

    /**
     * This method is for setting hooks that will be invoked on any
     * module that has a public method with the same name.
     * 
     * @param $hooks array The hooks you want to run when the forging process runs.
     * @return object This
     */
    public function setHooks($hooks) {
        $this->_hooks = $hooks;
        return $this;
    }

    /**
     * This method is used to get the hooks that have already been set.
     *
     * @return array The hooks that have been set.
     */
    public function getHooks() {
        return $this->_hooks;
    }

    /**
     * This method is used get the instanciated version of a class that has been
     * registered in a module.`
     *
     * @param $className string The class you would like to instanciate.
     * @return The instanciated object based on the $className.
     */
    public function get($className) {
        return $this->_resolveService($className);
    }

    /*
     * This method is used to check to see if a class has been registered to
     * be used with ulfberht.
     *
     * @param $className string The class you want to check
     */
    public function exists($className) {
        return (
            isset($this->_serviceModuleMap[$className])
            && !empty($this->_serviceModuleMap[$className])
        ) ? true : false;
    }

    /**
     * This method is used to get an instanciated module after it has been registered.
     *
     * @param $className string The name of the class you registered the module under.
     * $return object The instanciated module.
     */
    public function getModule($className) {
        if (!$this->isModule($className)) {
            throw new Exception('The module "' . $className . '" could not be found.');
        }
        return $this->_modules[$className];
    }

    /**
     * This method is used to register a module by its class name.
     *
     * @param $className string The class you want to register as a module.
     * @return object This
     */
    public function registerModule($className) {
        if (!class_exists($className)) {
            throw new Exception('Cannot find class "' . $className . '"');
        }
        //if module object doesn't exists attempt to create it first and register dependencies
        if (!isset($this->_modules[$className])) {
            $module = new $className();
            if (!$module instanceof module) {
                throw new Exception('"' . $className . '" does not inherit from ulfberht\core\ulfberhtModule');
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

        return $this;
    }

    /**
     * This method check to determine if a module has been registered.
     *
     * @param $className string The class name associated with the module you want to check for.
     */
    public function isModule($className) {
        return (
            isset($this->_modules[$className])
            && !empty($this->_modules[$className])
        ) ? true : false;
    }

    /**
     * This method is used to run all dependency checks on both modules and services and lock
     * down the application so no more modules/services can be added. Hooks are also ran.
     *
     * @param $hooks array The hooks you want to run on modules.
     * @return this
     */
    public function forge($hooks = null) {
        //set hooks
        if ($hooks && is_array($hooks)) {
            $this->setHooks($hooks);
        }
        //resolve all services
        foreach ($this->_modules as $moduleClassName => $module) {
            //run module dependency check for errors
            $this->_moduleDependencyErrorCheck($moduleClassName);
            //register all services for module
            foreach ($module->services as $serviceClassName => $service) {
                if (isset($this->_serviceModuleMap[$serviceClassName])) {
                    $errorMsg = 'Service "' . $serviceClassName . '" has already been defined.';
                    throw new Exception($errorMsg);
                } else {
                    $this->_serviceDependencyGraph->addResource($serviceClassName);
                    $this->_serviceDependencyGraph->addDependencies($serviceClassName, $service->dependencies);
                    $this->_serviceModuleMap[$serviceClassName] = $moduleClassName;
                }
            }
        }

        $moduleHooks = $this->getHooks();      
        foreach ($moduleHooks as $hook) {
            //run each module's hook method
            foreach ($this->_modules as $moduleClassName => $module) {
                $module->invoke($hook);
            }
        }

        $this->_forged = true;

        return $this;
    }

    /**
     * Returns if ulfberht has already been forged.
     *
     * @return boolean
     */
    public function isForged() {
        return $this->_forged;
    }

    /**
     * Kill the ulfberht instance.
     */
    public function destroy() {
        self::$_ulfberht = null;
    }

    /**
     * This method is used to resolve a service and all of its dependencies.
     *
     * @param $className The classname of the service you would like to resolve.
     * @return mixed The resolved service.
     */
    private function _resolveService($className) {
        $serviceDependencies = $this->_serviceDependencyErrorCheck($className);
        if (empty($serviceDependencies)) {
            return $this->_instanciateService($className, []);
        } else {
            $di = [];
            foreach ($serviceDependencies as $dependency) {
                //set $di[] with all dependencies and invoke
                $di[] = $this->_resolveService($dependency);
            }
            return $this->_instanciateService($className, $di);
        }
    }

    /**
     * This method is used to instanciate a service based on its instanciation plan.
     */
    private function _instanciateService($className, $resolvedDependencies) {
        $serviceModule = $this->_serviceModuleMap[$className];
        $module = $this->getModule($serviceModule);
        $service = $module->services[$className];
        switch ($service->constructorType) {
            case (service::SINGLETON_CONSTRUCTOR):
                if (!isset($this->_serviceCache[$className])) {
                    $classDef = $service->classDef;
                    $this->_serviceCache[$className] = $classDef->newInstanceArgs($resolvedDependencies);
                }
                return $this->_serviceCache[$className];
            case (service::FACTORY_CONSTRUCTOR):
                $classDef = $service->classDef;
                return $classDef->newInstanceArgs($resolvedDependencies);
        }
    }

    private function _moduleDependencyErrorCheck($className) {
        //check to see if there are errors resolving dependencies
        if ($error = $this->_moduleDependencyGraph->runDependencyCheck($className)) {
            switch ($error->code) {
                case 1:
                    $errorMsg = 'While trying to resolve module "' . $className . '", '
                        . 'Ulfberht found that the module dependency "' . $error->resourceId . '" '
                        . 'could not be found.';
                    throw new Exception($errorMsg);
                    break;
                case 2:
                    $errorMsg = 'While trying to resolve module "' . $className . '", '
                        . 'Ulfberht found that there was a cirular dependency caused by the module '
                        . '"' . $error->resourceId . '".';
                    throw new Exception($errorMsg);
                    break;
            }
        } else {
            $this->_moduleDependencyGraph->resetDepenencyCheck();
        }
    }

    private function _serviceDependencyErrorCheck($className) {
        if (!$this->exists($className)) {
            $errorMsg = 'The service "' . $className . '" could not be found.';
            throw new Exception($errorMsg);
        }
        //check to see if there are errors resolving dependencies
        else if ($error = $this->_serviceDependencyGraph->runDependencyCheck($className)) {
            switch ($error->code) {
                case 1:
                    $errorMsg = 'While trying to resolve service "' . $className . '", '
                        . 'Ulfberht found that the service dependency "' . $error->resourceId . '" '
                        . 'could not be found.';
                    throw new Exception($errorMsg);
                    break;
                case 2:
                    $errorMsg = 'While trying to resolve service "' . $className . '", '
                        . 'Ulfberht found that there was a cirular dependency caused by the service '
                        . '"' . $error->resourceId . '".';
                    throw new Exception($errorMsg);
                    break;
            }
        } else {
            $resolveOrder = $this->_serviceDependencyGraph->getDependencies($className);
            $this->_serviceDependencyGraph->resetDepenencyCheck();
            return $resolveOrder;
        }
    }
}
