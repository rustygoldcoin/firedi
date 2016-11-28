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
use ulfberht\core\ulfberhtException;

/**
 * This class defines a service to register to ulfberht.
 */
class serviceWrapper {

    const FACTORY_CONSTRUCTOR = 'factory';
    const SINGLETON_CONSTRUCTOR = 'singleton';

    /**
     * The ID of the service
     */
    public $serviceId;

    /**
     * A reflection object that defines this particular service.
     * @var ReflectionClass
     */
    public $classDef;

    /**
     * The build type of this particular service. Currently, you have two
     * build types that is supported by ulfberht. A 'singleton' and a 'factory'
     * @var string
     */
    public $constructorType;

    /**
     * An array that stores the names of the variables defined in the $closure.
     * The names of the variables of the $closure defines what services this
     * service depends on.
     * @var array
     */
    public $dependencies;

    /**
     * The constructor sets up the service object to be stored until zulfberht
     * determines that the service should be relocated into the ulfberht
     * runtime environment.
     * @param string $className The class you would like to wrap in an ulfberhtservice.
     * @param string $constructorType The type of constructor to use when you
     * instaniate the service.
     */
    public function __construct($className, $constructorType) {
        $this->serviceId = $className;
        $this->constructorType = $constructorType;
        $this->classDef = new ReflectionClass($className);
        $this->dependencies = [];

        //use reflection to get dependencies then store them
        $constructor = $this->classDef->getConstructor();
        if ($constructor) {
            $parameters = $constructor->getParameters();
            if (!empty($parameters)) {
                foreach ($parameters as $parameter) {
                    $dependency = $parameter->getClass();
                    if ($dependency) {
                        $this->dependencies[] = $dependency->getName();
                    } else {
                        $this->dependencies[] = '';
                    }
                }
            }
        }
    }
}
