<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once __DIR__ . '/x20.php';

use x20\core\x20;
use x20\core\x20module;
use x20\module\x20\service\config;

class myModule extends x20module {
    
    public function init() {
        $this->dependsOn('x20\module\x20');
    }
    
    public function start(config $config) {
        $config->set('test', 'It Worked!');
    }
    
    public function run(config $config) {
        var_dump($config->get('test'));
    }
    
}

x20()->registerModule('myModule');
x20()->start();
x20()->run();