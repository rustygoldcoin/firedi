<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once __DIR__ . '/x20.php';

use x20\core\x20;
use x20\core\x20module;

class dep1 {
    public function __construct(dep2 $dep2) {
        $dep2->changed = true;
        $this->changed = true;
    }
}

class dep2 {
    public function __construct() {
        $this->changed = false;
    }
}

class dep3 {
    public function __construct(dep2 $dep2) {
    }
}

class myModule extends x20module {
    
    public function init() {
        $this->registerSingleton('dep1');
        $this->registerSingleton('dep2');
        $this->registerSingleton('dep3');
    }
    
    public function start(dep1 $dep1, dep2 $dep) {
        var_dump($dep1);
        var_dump($dep);
    }
    
    public function run() {
        echo 'worked';
    }
    
}

x20()->registerModule('myModule');
x20()->start();
x20()->run();
x20()->getService('dep1');
x20()->getService('dep3');
