<?php

/**
 *    __  _____   ___   __          __
 *   / / / /   | <  /  / /   ____ _/ /_  _____
 *  / / / / /| | / /  / /   / __ `/ __ `/ ___/
 * / /_/ / ___ |/ /  / /___/ /_/ / /_/ (__  )
 * `____/_/  |_/_/  /_____/`__,_/_.___/____/
 *
 * @package FireDI
 * @author UA1 Labs Developers https://ua1.us
 * @copyright Copyright (c) UA1 Labs
 */

namespace UA1Labs\Fire;

use Fire\Test\TestCase;
use UA1Labs\Fire\Di;

class DiTest extends TestCase
{
    /**
     * Undocumented variable
     *
     * @var [type]
     */
    private $_di;

    public function beforeEach()
    {
        $this->_di = new Di();
    }

    public function afterEach()
    {
        unset($this->_di);
    }

    public function testConstructor()
    {
        $this
            ->should('Should not throw an execption and be an instance of Di')
            ->assert($this->_di instanceof Di);
    }

    public function testPut()
    {

    }
}

/**
 * Test classes for testing dependency injection
 */
Class TestClassA
{
    public function __construct(TestClassB $B) {}
}

Class TestClassB
{
    public function __construct(TestClassC $C) {}
}

Class TestClassC
{

}

Class TestClassD
{
    public function __construct(TestClassA $A) {}
}
