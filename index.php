<?php

// require_once __DIR__ . '/src/ulfberht.php';
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/src/debug.php';

use ulfberht\core\module;
use ulfberht\module\ulfberht;
use ulfberht\module\ulfberht\request;
use ulfberht\module\ulfberht\router;
use ulfberht\module\ulfberht\view;

class name {
    
    public function __construct(request $request, view $view) {
        $this->request = $request;
        $this->view = $view;
    }
    
    public function action() {
        $vm = ['name' => $this->request->attributes->get('name')];
        $this->view->render(__DIR__ . '/render.phtml', $vm);
    }
}

class myModule extends module {

    public function __construct() {
        $this->registerSingleton('name');
    }

    public function config(router $router) {
        $router->when('/name/{name}', 'name:action');
        $router->when('/abc', 'name:abc');
        $router->otherwise('name:404');
    }

    public function run() {
        ulfberht()->getModule('ulfberht\module\ulfberht')->invoke('mvc');
    }
}

ulfberht()->registerModule('myModule');
ulfberht()->forge();