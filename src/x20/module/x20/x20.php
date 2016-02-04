<?php
namespace x20\module;

use x20\core\x20module;

require_once __DIR__ . '/service/config.php';
x20()->registerModule('x20\module\x20');

class x20 extends x20module {
    
    public function init() {
        //register config service
        $this->registerSingleton('x20\module\x20\service\config');
    }
    
}