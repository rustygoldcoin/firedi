<?php

/**
 *    __  _____   ___   __          __
 *   / / / /   | <  /  / /   ____ _/ /_  _____
 *  / / / / /| | / /  / /   / __ `/ __ `/ ___/
 * / /_/ / ___ |/ /  / /___/ /_/ / /_/ (__  )
 * `____/_/  |_/_/  /_____/`__,_/_.___/____/
 *
 * @package FireDI
 * @author UA1 Labs Developers https://ua1.us
 * @copyright Copyright (c) UA1 Labs
 */

namespace UA1Labs\Fire\Di;

use ReflectionClass;
use UA1Labs\Fire\DiException;

/**
 * This class is meant to wrap a class being registered with FireDI.
 */
class ClassDefinition
{

    /**
     * The ID of the service
     *
     * @var string
     */
    public $serviceId;

    /**
     * A reflection object that defines this particular service.
     *
     * @var ReflectionClass
     */
    public $classDef;

    /**
     * An array that stores the names of the variables defined in the $closure.
     * The names of the variables of the $closure defines what services this
     * service depends on.
     *
     * @var array<string>
     */
    public $dependencies;

    /**
     * The constructor sets up the service object to be stored until zulfberht
     * determines that the service should be relocated into the ulfberht
     * runtime environment.
     *
     * @param string $classname The class you would like to wrap in an ulfberhtservice.
     * @return void
     */
    public function __construct($classname)
    {
        $this->serviceId = $classname;
        $this->classDef = new ReflectionClass($classname);
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
