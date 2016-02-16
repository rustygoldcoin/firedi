<?php

require_once __DIR__ . '/src/ulfberht.php';
require_once __DIR__ . '/src/debug.php';

use ulfberht\core\module;
use ulfberht\module\ulfberht\config;
use ulfberht\module\ulfberht\router;

class abc {
    public function __construct(router $router) {
        var_dump($router->getRouteVars());
    }
}

class myModule extends module {

    public function __construct() {
        $this->registerSingleton('abc');
    }

    public function config(router $a) {
        $a->when('/hello/:name', 'abc:action');
        $a->otherwise('xyz');
    }

    public function run(router $a) {
        var_dump($a->resolveRoute());
        var_dump($a->getRouteVars('name'));
        var_dump($a->getCurrentRoute());
        var_dump($a->getMatchedRoute());
    }
}

ulfberht()->registerModule('myModule');
ulfberht()->go();
ulfberht()->getModule('ulfberht\module\ulfberht')->invoke('mvc');