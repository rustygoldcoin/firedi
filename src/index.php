<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once __DIR__ . '/x20.php';

use x20\core\x20;
use x20\core\x20module;

class dep1 {
    
}

class dep2 {
    
}

class abc extends x20module {
    public function init() {
        $this->registerModule('xyz');
    }
}
class xyz extends x20module {
    public function init() {
        $this->registerModule('myModule');
    }
}   

class myModule extends x20module {
    
    public function init() {
        $this->registerModule('abc');
        $this->registerModule('xyz');
        $this->registerFactory('dep1');
        $this->registerSingleton('dep2');
    }
    
}

x20()->registerModule('myModule');
x20()->start();
x20()->run();