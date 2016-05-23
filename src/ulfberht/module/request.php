<?php
/**
 * @package ulfberht
 * @author Joshua L. Johnson <josh@ua1.us>
 * @link http://ua1.us
 * @copyright Copyright 2016, Joshua L. Johnson
 * @license MIT
 */
 
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