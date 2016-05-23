<?php

require_once __DIR__ . '/../../../vendor/autoload.php';

use ulfberht\core\module;
use ulfberht\module\router;
use ulfberht\module\request;

class myModule extends module {
    
    public function __construct() {
        $this->dependsOn('ulfberht\module');
        $this->registerSingleton('controller');
    }
    
    public function config(router $router) {
        $router->when('/{name}', 'controller:action');
    }
    
}

class controller {
    
    public function __construct(request $request) {
        $this->request = $request;
    }
    
    public function action() {
        echo 'Hello ' . $this->request->attributes->get('name');
    }
    
}

ulfberht()->registerModule('myModule');
ulfberht()->forge([
    'config',
    'mvc'
]);