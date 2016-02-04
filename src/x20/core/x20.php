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
use x20\core\x20service;

/**
 * The x20 Class is what makes x20 possible. This class handles the
 * entire Dependency Injection environment.
 */
class x20
{

    private static $_x20;

    private $_modules;
    
    private $_serviceModuleMap;

    private $_serviceCache;

    private $_moduleDependencyGraph;

    private $_serviceDependencyGraph;

    private function __construct() {
        $this->_modules = [];
        $this->_serviceModuleMap = [];
        $this->_serviceCache = [];
        $this->_moduleDependencyGraph = new x20graph();
        $this->_serviceDependencyGraph = new x20graph();
    }

    public static function getInstance() {
        if (!isset(self::$_x20) || !(self::$_x20)) {
            self::$_x20 = new self();
        }

        return self::$_x20;
    }
    
    public function getService($className) {
        return $this->_resolveService($className);
    }
    
    public function isService($className) {
        return (
            isset($this->_serviceModuleMap[$className])
            && !empty($this->_serviceModuleMap[$className])
        ) ? true : false;
    }
    
    public function getModule($className) {
        if ($this->isModule($className)) {
            return $this->_modules[$className];
        }
        return false;
    }

    public function isModule($className) {
        return (
            isset($this->_modules[$className])
            && !empty($this->_modules[$className])
        ) ? true : false;
    }

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
        //run each module's start method
        foreach ($this->_modules as $moduleClassName => $module) {
            $module->invoke('start');
        }
    }
    
    public function run() {
        foreach ($this->_modules as $moduleClassName => $module) {
            $module->invoke('run');
        }
    }
    
    public function destroy() {
        self::$x20 = null;
    }
    
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
    
    private function _instanciateService($className, $resolvedDependencies) {
        $serviceModule = $this->_serviceModuleMap[$className];
        $module = $this->getModule($serviceModule);
        $service = $module->services[$className];
        switch ($service->constructorType) {
            case (x20service::SINGLETON_CONSTRUCTOR):
                if (!isset($this->_serviceCache[$className])) {
                    $classDef = $service->classDef;
                    $this->_serviceCache[$className] = $classDef->newInstanceArgs($resolvedDependencies);
                }
                return $this->_serviceCache[$className];
            case (x20service::FACTORY_CONSTRUCTOR):
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
        } else {
            $this->_moduleDependencyGraph->resetDepenencyCheck();
        }
    }
    
    private function _serviceDependencyErrorCheck($className) {
        if (!$this->isService($className)) {
            $errorMsg = 'The service "' . $className . '" could not be found.';
            throw new Exception($errorMsg);
        }
        //check to see if there are errors resolving dependencies
        else if ($error = $this->_serviceDependencyGraph->runDependencyCheck($className)) {
            switch ($error->code) {
                case 1:
                    $errorMsg = 'While trying to resolve service "' . $className . '", '
                        . 'x20 found that the service dependency "' . $error->resourceId . '" '
                        . 'could not be found.';
                    throw new Exception($errorMsg);
                    break;
                case 2:
                    $errorMsg = 'While trying to resolve service "' . $className . '", '
                        . 'x20 found that there was a cirular dependency caused by the service '
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
