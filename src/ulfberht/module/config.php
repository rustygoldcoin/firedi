<?php
/**
 * @package ulfberht
 * @author Joshua L. Johnson <josh@ua1.us>
 * @link http://ua1.us
 * @copyright Copyright 2016, Joshua L. Johnson
 * @license MIT
 */

namespace ulfberht\module;

class config {

    private $_config;

    public function __construct() {
        $this->_config = [];
    }

    public function set($key, $config) {
        $this->_config[$key] = $config;
    }

    public function get($key) {
        if(isset($this->_config[$key])) {
            return $this->_config[$key];
        }
        return false;
    }

}