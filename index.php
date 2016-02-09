<?php

require_once __DIR__ . '/src/ulfberht.php';
require_once __DIR__ . '/src/debug.php';

use ulfberht\core\module;
use ulfberht\module\ulfberht\config;


class myModule extends module {
    
    public function start(config $conf) {
        var_dump($conf);
        $conf->set('add', 'true');
    }
    
    public function run(config $conf) {
        var_dump($conf->get('add'));
    }
    
}

ulfberht()->registerModule('myModule');
ulfberht()->start();
ulfberht()->run();