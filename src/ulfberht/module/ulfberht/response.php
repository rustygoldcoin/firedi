<?php

namespace ulfberht\module\ulfberht;

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