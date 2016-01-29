<?php

namespace x20\core;

use x20\core\x20service;

/**
 * The x20module class is used to define a x20 module in x20.
 */
class x20module {

    /**
     * The unique ID of this particular module
     *
     * @var string
     */
    public $id;

    /**
     * An array that contains the list of defined service objects.
     *
     * @var array
     */
    public $services;

    /**
     * The constructor that sets up the module object.
     *
     * @param string $module_id A unique ID that identifies this module.
     */
    public function __construct($module_id) {
        $this->id = $module_id;
        $this->services = [];
    }

    /**
     * This method is used to define a service that runs when a module is loaded
     * into the z20 runtime environment.
     *
     * @param callable $closure A closure that defines the service
     * @return x20module
     */
    public function init($closure) {
        $this->services[$this->id . '_init'] = new z20Service($this->id . '_run', 'run', 'factory', $closure);
        return $this;
    }

    /**
     * This method is used to define a service that runs when the z20::execute()
     * is invoked. This service will only run if the module has been loaded into
     * the z20 runtime environment.
     *
     * @param callable $closure A closure that defines the service
     * @return z20Module
     */
    public function execute($closure) {
        $this->services[$this->id . '_exec'] = new z20Service($this->id . '_exec', 'exec', 'factory', $closure);
        return $this;
    }

    /**
     * This method is used to define a service that returns a singleton instance
     * of the service that was defined in the $closure.
     *
     * @param string $singleton_id The name of the service you would like to register
     * @param callable $closure A closure that defines the service
     * @return z20Module
     */
    public function singleton($singleton_id, $closure) {
        $this->services[$singleton_id] = new z20Service($singleton_id, 'singleton', 'singleton', $closure);
        return $this;
    }

    /**
     * This method is used to define a service that returns a new instance
     * of the service that was defined in the $closure everytime it is called.
     *
     * @param string $factory_id The name of the service you would like to register
     * @param callable $closure A closure that defines the service
     * @return z20Module
     */
    public function factory($factory_id, $closure) {
        $this->services[$factory_id] = new z20Service($factory_id, 'factory', 'factory', $closure);
        return $this;
    }

}
