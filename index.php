<?php

require_once __DIR__ . '/vendor/autoload.php';

class ClassB {}
class ClassC {}

class ClassA {

    public function __construct(ClassB $dep1, ClassC $dep2) {
        $this->dep1 = $dep1;
        $this->dep2 = $dep2;
    }

}

var_dump(ulfberht()
    ->factory('ClassA')
    ->factory('ClassB')
    ->factory('ClassC')
    ->get('ClassA'));
