<?php

namespace x20\core;

use ReflectionClass;
use Exception;

/**
 * The z20Service class defines a service to register to z20 within modules.
 *
 * @package z20
 * @link http://joshua.lylejohnson.us/z20
 * @author Joshua L. Johnson <joshua@lylejohnson.us>
 * @copyright Copyright 2013-2015, Joshua L. Johnson
 * @license GPL2
 */
class x20service {
    
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
    public $classDef;

    /**
     * The build type of this particular service. Currently, you have two
     * build types that is supported by z20. A 'singleton' and a 'factory'
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
     * The constructor sets up the service object to be stored until zx20
     * determines that the service should be relocated into the x20
     * runtime environment.
     *
     * @param string $classDef The class you would like to wrap in an x20service.
     * @param string $constructorType The type of constructor to use when you 
     * instaniate the service.
     */
    public function __construct($classDef, $constructorType) {
        $this->serviceId = $classDef;
        $this->constructorType = $constructorType;
        //make sure class exists
        if (class_exists($classDef)) {
            $this->classDef = new ReflectionClass($classDef);
        } else {
            throw new Error('Cannot find class ' . $classDef);
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
                        $this->dependencies[] = $dependency;
                    } else {
                        $error = 'While trying to establish dependencies for class ' .'"' . $classDef . '", ' . 
                        'x20 has found a parameter that has not hinted a class for parameter "$' . $parameter->getName() . '".';
                        throw new Exception($error);
                    }
                }
            }
        }
    }

}
