<?php
namespace x20\module\x20\service;

use x20\core\x20service;

class config extends x20service {
    
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