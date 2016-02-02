<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once __DIR__ . '/x20.php';

use x20\core\x20;
use x20\core\x20module;

class dep1 {
    public function __construct(dep2 $dep2) {
        var_dump($dep2);
    }
}

class dep2 {
    
}

class dep3 {
    
}

class abc extends x20module {
    public function init() {
        $this->registerModule('xyz');
    }
    
    public function start(dep3 $dp3) {
        var_dump($dep3);
        $this->loaded = true;
    }
    
    public function run(dep3 $dep3) {
        var_dump($dep3);
    }
}
class xyz extends x20module {
    public function init() {
        $this->registerFactory('dep3');
    }
}   

class myModule extends x20module {
    
    public function init() {
        $this->registerModule('abc');
        $this->registerFactory('dep1');
        $this->registerSingleton('dep2');
    }
    
}

x20()->registerModule('myModule');
x20()->start();
x20()->run();
x20()->getService('dep1');