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

namespace Test\UA1Labs\Fire;

use Fire\Test\TestCase;
use UA1Labs\Fire\Di;
use UA1Labs\Fire\DiException;
use Throwable;

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
        $this->should('The constructor should not throw an execption and be an instance of Di.');
        $this->assert($this->_fireDi instanceof Di);
    }

    public function testPutObject()
    {
        $this->should('Put an object in the cache without an exception.');
        try {
            $this->_fireDi->put('TestObject', new TestClassC());
            $this->assert(true);
        } catch (DiException $e) {
            $this->assert(false);
        }
    }

    public function testGetObject()
    {
        $this->should('Resolve all dependencies for TestClassA and return the TestClassA object.');
        $testClassA = $this->_fireDi->get('Test\UA1Labs\Fire\TestClassA');
        $this->assert($testClassA instanceof TestClassA);
    }

    public function testGetWithObject()
    {
        $this->should('Return a TestClassB object.');
        $testClassC = $this->_fireDi->get('Test\UA1Labs\Fire\TestClassC');
        $dependencies = [$testClassC];
        $testClassB = $this->_fireDi->getWith('Test\UA1Labs\Fire\TestClassB', $dependencies);
        $this->assert($testClassB instanceof TestClassB);

        $this->should('Prove that TestClassB has a $C variable that is TestClassC.');
        $this->assert($testClassB->C instanceof TestClassC);

        $this->should('Prove that the object cache does not contain a TestClassB');
        $objectCache = $this->_fireDi->getObjectCache();
        $this->assert(!isset($objectCache['TestClassB']));

        $this->should('Throw an exception because the dependencies have not been resolved.');
        try {
            $this->_fireDi->getWith('Test\UA1Labs\Fire\TestClassA', []);
            $this->assert(false);
        } catch (Throwable $e) {
            $this->assert(true);
        }

        $this->should('Throw and exception if a the class you are trying to get does not exists.');
        try {
            $this->_fireDi->getWith('UndefinedClass', []);
            $this->assert(false);
        } catch (Throwable $e) {
            $this->assert(true);
        }
    }

    public function testGetObjectCache()
    {
        $this->_fireDi->put('TestObject', new TestClassC());
        $objectCache = $this->_fireDi->getObjectCache();

        $this->should('Return an object cache array with a key "TestObject".');
        $this->assert(isset($objectCache['TestObject']));

        $this->should('Return an object cache with the object we put into it.');
        $this->assert($objectCache['TestObject'] instanceof TestClassC);
    }

    public function testClearObjectCache()
    {
        $this->should('Remove all objects from the object cache');
        $this->_fireDi->put('TestObject', new TestClassC());
        $this->_fireDi->clearObjectCache();
        $objectCache = $this->_fireDi->getObjectCache();
        $this->assert(empty($objectCache));
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
    public $C;

    public function __construct(TestClassC $C) {
        $this->C = $C;
    }
}

Class TestClassC
{

}

Class TestClassD
{
    public function __construct(TestClassA $A, TestClassB $B) {}
}
