<?php

/**
 * @package ulfberht
 * @author Joshua L. Johnson <josh@ua1.us>
 * @link http://labs.ua1.us
 * @copyright Copyright 2016, Joshua L. Johnson
 * @license MIT
 */

namespace ulfberht\core;

use ReflectionClass;
use Exception;

/**
 * The ulfbertService class defines a service to register to ulfberht within modules.
 */
class service {
    
    const FACTORY_CONSTRUCTOR = 'factory';
    const SINGLETON_CONSTRUCTOR = 'singleton';

    /**
     * The ID of the service
     */
    public $serviceId;

    /**
     * A closure object that defines this particular service.
     *
     * @var callable
     */
    public $className;

    /**
     * The build type of this particular service. Currently, you have two
     * build types that is supported by ulfberht. A 'singleton' and a 'factory'
     *
     * @var string
     */
    public $constructorType;

    /**
     * An array that stores the names of the variables defined in the $closure.
     * The names of the variables of the $closure defines what services this
     * service depends on.
     *
     * @var array
     */
    public $dependencies;

    /**
     * The constructor sets up the service object to be stored until zulfberht
     * determines that the service should be relocated into the ulfberht
     * runtime environment.
     *
     * @param string $className The class you would like to wrap in an ulfberhtservice.
     * @param string $constructorType The type of constructor to use when you 
     * instaniate the service.
     */
    public function __construct($className, $constructorType) {
        $this->serviceId = $className;
        $this->constructorType = $constructorType;
        //make sure class exists
        if (class_exists($className)) {
            $this->classDef = new ReflectionClass($className);
        } else {
            throw new Exception('Cannot find class "' . $className . '"');
        }

        //use reflection to get dependencies then store them
        $this->dependencies = [];
        $constructor = $this->classDef->getConstructor();
        if ($constructor) {
            $parameters = $constructor->getParameters();
            if (!empty($parameters)) {
                foreach ($parameters as $parameter) {
                    $dependency = $parameter->getClass();
                    if ($dependency) {
                        $this->dependencies[] = $dependency->getName();
                    } else {
                        $error = 'While trying to establish dependencies for class "' . $className . '", ' . 
                        'ulfberht has found a parameter that has not hinted a class for parameter "$' . $parameter->getName() . '".';
                        throw new Exception($error);
                    }
                }
            }
        }
    }

}
