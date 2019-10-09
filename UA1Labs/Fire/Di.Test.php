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
     * The FireDi
     *
     * @var Di
     */
    private $_fireDi;

    public function beforeEach()
    {
        $this->_fireDi = new Di();
    }

    public function afterEach()
    {
        unset($this->_fireDi);
    }

    public function testConstructor()
    {
        $this
            ->should('The constructor should not throw an execption and be an instance of Di.')
            ->assert($this->_fireDi instanceof Di);
    }

    public function testPutObject()
    {
        $this->should('Should put an object in the cache without an exception.');
        $this->_fireDi->put('TestObject', []);
        $this->assert(true);

        $this->should('The object cache should contain a key "TestObject"');
        $objectCache = $this->_fireDi->getObjectCache();
        $this->assert(isset($objectCache['TestObject']));

        $this->should('The "TestObject" in object cache should be an array');
        $this->assert(is_array($objectCache['TestObject']));
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
