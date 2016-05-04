<?php

namespace ulfberht\module;

use Symfony\Component\HttpFoundation\Request as SymfonyRequestClass;

class request extends SymfonyRequestClass {

    public function __construct() {
        parent::__construct(
            $_GET,
            $_POST,
            array(),
            $_COOKIE,
            $_FILES,
            $_SERVER
        );
    }

}