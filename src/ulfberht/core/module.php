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
use ulfberht\core\ulfberht;
use ulfberht\core\service;

/**
 * The ulfberhtmodule class is used to define a ulfberht module in ulfberht.
 */
abstract class module {

    /**
     * An array that contains the list of defined service objects.
     *
     * @var array
     */
    public $services = [];
    
    /**
     * An array that contains a list of modular dependencies
     *
     * @var array
     */
    public $modules = [];
    
    /**
     * This method is used to register a module as a dependnecy to this module.
     *
     * @param string The module class
     */
    public function dependsOn($className) {
        $this->modules[] = $className;
    }

    /**
     * This method is used to define a service that returns a singleton instance
     * of the service that was defined.
     *
     * @param string $className The name of the class you would like to register
     * @return ulfberhtmodule
     */
    public function registerSingleton($className) {
        $this->services[$className] = new service($className, service::SINGLETON_CONSTRUCTOR);
            return $this;
    }

    /**
     * This method is used to define a service that returns a new instance
     * of the service that was defined everytime it is called.
     *
     * @param string $factory_id The name of the service you would like to register
     * @return ulfberhtmodule
     */
    public function registerFactory($className) {
        $this->services[$className] = new service($className, service::FACTORY_CONSTRUCTOR);
        return $this;
    }
    
    /*
     * This method is used to invoke a public method on this object and inject it dependencies
     * defined as parameters on the method.
     *
     * @param string The method name you want to invoke on the module.
     */
    public function invoke($methodName) {
        if (method_exists($this, $methodName)) {
            $reflect = new ReflectionClass($this);
            $moduleMethod = $reflect->getMethod($methodName);
            $parameters = $moduleMethod->getParameters();
            if (!empty($parameters)) {
                $di = [];
                foreach ($parameters as $parameter) {
                    $dependency = $parameter->getClass();
                    if ($dependency) {
                        $serviceClassName = $dependency->getName();
                        $di[] = ulfberht()->get($serviceClassName);
                    }
                }
                $moduleMethod->invokeArgs($this, $di);
            } else {
                $moduleMethod->invoke($this);
            }
        }
    }

}
