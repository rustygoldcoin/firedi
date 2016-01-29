<?php

namespace z20\core;

use ReflectionFunction;

/**
 * The z20Service class defines a service to register to z20 within modules.
 *
 * @package z20
 * @link http://joshua.lylejohnson.us/z20
 * @author Joshua L. Johnson <joshua@lylejohnson.us>
 * @copyright Copyright 2013-2015, Joshua L. Johnson
 * @license GPL2
 */
class z20Service {

    /**
     * The unique ID of this particular service.
     *
     * @var string
     */
    public $service_id;

    /**
     * The type of service this particular service is. Example: 'singleton' or
     * 'factory'
     *
     * @var string
     */
    public $service_type;

    /**
     * The build type of this particular service. Currently, you have two
     * build types that is supported by z20. A 'singleton' and a 'factory'
     *
     * @var string
     */
    public $build_type;

    /**
     * An array that stores the names of the variables defined in the $closure.
     * The names of the variables of the $closure defines what services this
     * service depends on.
     *
     * @var array
     */
    public $dependencies;

    /**
     * A closure object that defines this particular service.
     *
     * @var callable
     */
    public $closure;

    /**
     * The constructor sets up the service object to be stored until z20
     * determines that the service should be relocated into the z20
     * runtime environment.
     *
     * @param string $service_id The unique ID of the service
     * @param string $service_type The service type that defines how the
     * service is to be used within z20.
     * @param string $build_type The build type used to determine how z20
     * should handle the creation practice.
     * @param callable $closure The closure that defines the service.
     */
    public function __construct($service_id, $service_type, $build_type, $closure) {
        $this->service_id = $service_id;
        $this->service_type = $service_type;
        $this->build_type = $build_type;
        $this->closure = new ReflectionFunction($closure);

        //use reflection to get dependencies then store them as property
        $this->dependencies = array();
        $parameters = $this->closure->getParameters();
        if (!empty($parameters)) {
            foreach ($parameters as $parameter) {
                $this->dependencies[] = $parameter->getName();
            }
        }
    }

}
