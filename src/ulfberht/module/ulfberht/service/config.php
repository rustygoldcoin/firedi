<?php

/**
 * @package ulfberht
 * @author Joshua L. Johnson <josh@ua1.us>
 * @link http://labs.ua1.us
 * @copyright Copyright 2016, Joshua L. Johnson
 * @license MIT
 */
 
namespace ulfberht\module\ulfberht;

class config {
    
    private $_config;
    
    public function __construct() {
        $this->_config = [];
    }
    
    public function set($key, $config) {
        $this->config[$key] = $config;
    }
    
    public function get($key) {
        return $this->config[$key];
    }
    
}