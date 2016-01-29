<?php
require_once 'x20/core/x20service.php';
require_once 'x20/core/x20module.php';

use x20\core\x20module;

class myClass {
    public function __construct(x20module $myClass) {
        
    }
}

use x20\core\x20service;

$service = new x20service('myClass', x20service::FACTORY_CONSTRUCTOR);
echo '------------------------------------';
var_dump($service);