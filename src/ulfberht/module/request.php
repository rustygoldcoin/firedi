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

/** 
 * This class is meant to wrap The Symfony Request object.
 */
class request extends SymfonyRequestClass {

    /**
     * The constructor.
     */
    public function __construct() {
        //setup the standard request.
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