<?php
/**
 * @package ulfberht
 * @author Joshua L. Johnson <josh@ua1.us>
 * @link http://ua1.us
 * @copyright Copyright 2016, Joshua L. Johnson
 * @license MIT
 */
 
namespace ulfberht\module;

use Symfony\Component\HttpFoundation\Response as SymfonyResponseClass;

class response extends SymfonyResponseClass {

    public function __construct() {
        parent::__construct(
            '',
            200,
            []
        );
    }

}