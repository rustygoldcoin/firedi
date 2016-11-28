<?php
/**
 * @package ulfberht
 * @author Joshua L. Johnson <josh@ua1.us>
 * @link http://ua1.us
 * @copyright Copyright 2016, Joshua L. Johnson
 * @license MIT
 */

namespace ulfberht\core;

use ReflectionClass;
use ulfberht\core\graph;
use ulfberht\core\serviceWrapper;
use ulfberht\core\ulfberhtException;

/**
 * The ulfberht class is what makes ulfberht possible. This class handles the
 * entire Dependency Injection environment.
 */
class ulfberht {

    const ERROR_SERVICE_NOT_FOUND = 'The service "%s" could not be found.';
    const ERROR_CLASS_NOT_FOUND = 'Class "%s" does not exist and cannot'
        . ' be registered with Ulfberht.';
    const ERROR_CLASS_ALREADY_DEFINED = 'Class "%s" has already been registered'
        . ' with Ulfberht.';
    const ERROR_CIRCULAR_DEPENDENCY = 'While trying to resolve service "%s",'
        . ' Ulfberht found that there was a cirular dependency caused by the service'
        . ' "%s".';
    const ERROR_DEPENDENCY_NOT_FOUND = 'While trying to resolve service "%s",'
        . ' Ulfberht found that the service dependency "%s"'
        . ' could not be found.';

    /**
     * The ulfberht instance object.
     * @var object
     */
    private static $_ulfberht;

    /**
     * An array that stores all service definitions.
     * @var array
     */
    private $_services;

    /**
     * Stores singleton object services that have been
     * registered as singleton type service.
     * @var array
     */
    private $_serviceCache;

    /**
     * A dependency graph containing information about services
     * and their dependencies.
     * @var \ulfberht\core\graph
     */
    private $_serviceDependencyGraph;

    /**
     * The constructor.
     */
    private function __construct() {
        $this->_services = [];
        $this->_serviceCache = [];
        $this->_serviceDependencyGraph = new graph();
    }

    /**
     * Gets the singleton instance of this class.
     * @return object uflberht\core\ulfberht
     */
    public static function instance() {
        if (!isset(self::$_ulfberht) || !(self::$_ulfberht)) {
            self::$_ulfberht = new self();
        }
        return self::$_ulfberht;
    }

    /**
     * This method is used to define a service that returns a singleton instance
     * of the service that was defined.
     * @param string $className The name of the class you would like to register
     * @return ulfberht\core\ulfberht
     */
    public function singleton($className) {
        $this->_registerService($className, serviceWrapper::SINGLETON_CONSTRUCTOR);
        return $this;
    }

    /**
     * This method is used to define a service that returns a new instance
     * of the service that was defined everytime it is called.
     * @param string $factory_id The name of the service you would like to register
     * @return ulfberht\core\ulfberht
     */
    public function factory($className) {
        $this->_registerService($className, serviceWrapper::FACTORY_CONSTRUCTOR);
        return $this;
    }

    /**
     * This method is used get the instanciated version of a class that has been
     * registered in a module.
     * @param $className string The class you would like to instanciate
     * @return object The instanciated object based on the $className
     */
    public function get($className) {
        if (!$this->has($className)) {
            $errorMsg = sprintf(self::ERROR_SERVICE_NOT_FOUND, $className);
            throw new ulfberhtException($errorMsg);
        }
        //return service from service cache if it is there
        if (isset($this->_serviceCache[$className])) {
            return $this->_serviceCache[$className];
        }

        return $this->_resolveService($className);
    }

    /**
     * This method is used to check to see if a class has been registered to
     * be used with ulfberht.
     * @param $className string The class you want to check
     * @return boolean
     */
    public function has($className) {
        return isset($this->_services[$className]);
    }

    /**
     * Destroy the ulfberht instance.
     * @return void
     */
    public function destroy() {
        self::$_ulfberht = null;
    }

    /**
     * Registers a new service into ulfberht.
     * @param string $className The class name you would like to register
     * @param string $constructorType The constructor type
     * @return void
     */
    private function _registerService($className, $constructorType) {
        if (!class_exists($className)) {
            $errorMsg = sprintf(self::ERROR_CLASS_NOT_FOUND, $className);
            throw new ulfberhtException($errorMsg);
        } elseif ($this->has($className)) {
            $errorMsg = sprintf(self::ERROR_CLASS_ALREADY_DEFINED, $className);
            throw new ulfberhtException($errorMsg);
        }

        $this->_services[$className] = new serviceWrapper(
            $className,
            $constructorType
        );
        $this->_serviceDependencyGraph->addResource($className);
        $this->_serviceDependencyGraph->addDependencies(
            $className,
            $this->_services[$className]->dependencies
        );
    }

    /**
     * This method is used to resolve a service and all of its dependencies.
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
        $service = $this->_services[$className];
        switch ($service->constructorType) {
            case (serviceWrapper::SINGLETON_CONSTRUCTOR):
                if (!isset($this->_serviceCache[$className])) {
                    $classDef = $service->classDef;
                    $this->_serviceCache[$className] = $classDef->newInstanceArgs($resolvedDependencies);
                }
                return $this->_serviceCache[$className];
            case (serviceWrapper::FACTORY_CONSTRUCTOR):
                $classDef = $service->classDef;
                return $classDef->newInstanceArgs($resolvedDependencies);
        }
    }

    private function _serviceDependencyErrorCheck($className) {
        if ($error = $this->_serviceDependencyGraph->runDependencyCheck($className)) {
            switch ($error->code) {
                case 1:
                    $errorMsg = sprintf(self::ERROR_DEPENDENCY_NOT_FOUND, $className, $error->resourceId);
                    throw new ulfberhtException($errorMsg);
                    break;
                case 2:
                    $errorMsg = sprintf(self::ERROR_CIRCULAR_DEPENDENCY, $className, $error->resourceId);
                    throw new ulfberhtException($errorMsg);
                    break;
            }
        } else {
            $resolveOrder = $this->_serviceDependencyGraph->getDependencies($className);
            $this->_serviceDependencyGraph->resetDepenencyCheck();
            return $resolveOrder;
        }
    }
}
