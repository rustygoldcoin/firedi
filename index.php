<?php

// require_once __DIR__ . '/src/ulfberht.php';
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/src/debug.php';

use ulfberht\core\module;
use ulfberht\module\ulfberht;
use ulfberht\module\ulfberht\config;
use ulfberht\module\ulfberht\doctrine;

class myModule extends module {

    public function __construct() {}

    public function config(config $config) {
        $config->set('doctrine', [
            'default' => [
                'develop' => false,
                'type' => 'annotation',
                'paths' => ['/src'],
                'database' => [
                    'driver' => 'pdo_mysql',
                    'user' => 'abc',
                    'pass' => '123',
                    'dbname' => 'name'
                ]
            ]
        ]);
    }

    public function run(doctrine $doctrine) {
        var_dump($doctrine->get('default'));
    }
}

ulfberht()->registerModule('myModule');
ulfberht()->forge();