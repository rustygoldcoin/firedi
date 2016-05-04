<?php

require_once __DIR__ . '/vendor/autoload.php';
ulfberht\debug::enable();

use ulfberht\core\module as baseModule;
use ulfberht\module;
use ulfberht\module\request;
use ulfberht\module\router;
use ulfberht\module\view;

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
        ulfberht('ulfberht\module\ulfberht')->invoke('mvc');
    }
}

ulfberht()->registerModule('myModule');
ulfberht()->setHooks([
    'config',
    'mvc'
]);
ulfberht()->forge();