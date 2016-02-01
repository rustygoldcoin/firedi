<?php

/**
 * @package x20
 * @author Joshua L. Johnson <josh@ua1.us>
 * @link http://labs.ua1.us
 * @copyright Copyright 2016, Joshua L. Johnson
 * @license MIT
 */

namespace x20\core;

use x20\core\x20service;

/**
 * The x20module class is used to define a x20 module in x20.
 */
abstract class x20module {

    /**
     * An array that contains the list of defined service objects.
     *
     * @var array
     */
    public $services;
    
    /**
     * An array that contains a list of modular dependencies
     *
     * @var array
     */
    public $modules;

    /**
     * The constructor that sets up the module object.
     */
    public function __construct() {
        $this->services = [];
        $this->modules = [];
        $this->init();
    }
    
    /**
     * This method is called when the module is first initialized. Use
     * this method to define dependencies and services.
     */
    public function init() {}
    
    /**
     * This method is called when the x20::start() is executed.
     */
    public function start() {}
    
    /**
     * This method is called when x20::run() is executed.
     */    
    public function run() {}
    
    /**
     * This method is used to register a module as a dependnecy to this module.
     *
     * @param string The module class
     */
    public function registerModule($className) {
        $this->modules[] = $className;
    }

    /**
     * This method is used to define a service that returns a singleton instance
     * of the service that was defined.
     *
     * @param string $className The name of the class you would like to register
     * @return x20module
     */
    public function registerSingleton($className) {
        $this->services[$className] = new x20service($className, x20service::SINGLETON_CONSTRUCTOR);
        return $this;
    }

    /**
     * This method is used to define a service that returns a new instance
     * of the service that was defined everytime it is called.
     *
     * @param string $factory_id The name of the service you would like to register
     * @return x20module
     */
    public function registerFactory($className) {
        $this->services[$className] = new x20service($className, x20service::FACTORY_CONSTRUCTOR);
        return $this;
    }

}
