<?php

require_once __DIR__ . '/src/ulfberht.php';
require_once __DIR__ . '/src/debug.php';

use ulfberht\core\module;
use ulfberht\module\ulfberht\config;
use ulfberht\module\ulfberht\router;


class myModule extends module {
    
    public function start(config $conf) {
        var_dump($conf);
        $conf->set('add', 'true');
    }
    
    public function run(config $conf) {
        var_dump($conf->get('add'));
        $conf->loaded=true;
    }
    
    public function myRun(config $conf, router $router) {
        var_dump($conf);
        var_dump($router);
    }
    
}

ulfberht()->registerModule('myModule');
ulfberht()->start();
ulfberht()->getModule('myModule')->invoke('myRun');
ulfberht()->run();