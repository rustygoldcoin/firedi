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

use UA1Labs\Fire\Test\TestCase;
use UA1Labs\Fire\Di;
use UA1Labs\Fire\DiException;
use Exception;

class DiTestCase extends TestCase
{
    /**
     * The FireDi
     *
     * @var Di
     */
    private $fireDi;

    public function beforeEach()
    {
        $this->fireDi = new Di();
    }

    public function afterEach()
    {
        unset($this->fireDi);
    }

    public function testConstructor()
    {
        $this->should('The constructor should not throw an execption and be an instance of Di.');
        $this->assert($this->fireDi instanceof Di);
    }

    public function testPutObject()
    {
        $this->should('Put an object in the cache without an exception.');
        try {
            $this->fireDi->put('TestObject', new TestClassC());
            $this->assert(true);
        } catch (DiException $e) {
            $this->assert(false);
        }
    }

    public function testGetObject()
    {
        $this->should('Resolve all dependencies for TestClassA and return the TestClassA object.');
        $testClassA = $this->fireDi->get('Test\UA1Labs\Fire\TestClassA');
        $this->assert($testClassA instanceof TestClassA);

        $this->should('Have placed TestClassA, TestClassB, and TestClassC within the object cache.');
        $objectCache = $this->fireDi->getObjectCache();
        $this->assert(
            isset($objectCache['Test\UA1Labs\Fire\TestClassA'])
            && isset($objectCache['Test\UA1Labs\Fire\TestClassB'])
            && isset($objectCache['Test\UA1Labs\Fire\TestClassC'])
            && $objectCache['Test\UA1Labs\Fire\TestClassA'] instanceof TestClassA
            && $objectCache['Test\UA1Labs\Fire\TestClassB'] instanceof TestClassB
            && $objectCache['Test\UA1Labs\Fire\TestClassC'] instanceof TestClassC
        );

        $this->fireDi->clearObjectCache();

        $this->should('Resolve all dependencies for TestClassD and return it.');
        $testClassD = $this->fireDi->get('Test\UA1Labs\Fire\TestClassD');
        $this->assert($testClassD instanceof TestClassD);

        $this->should('Have set ::A as TestClassA on TestClassD object.');
        $this->assert(isset($testClassD->A) && $testClassD->A instanceof TestClassA);

        $this->should('Have set ::B as TestClassB on TestClassD object.');
        $this->assert(isset($testClassD->B) && $testClassD->B instanceof TestClassB);

        $this->should('Have set ::C as TestClassC on TestClassB.');
        $this->assert(isset($testClassD->B->C) && $testClassD->B->C instanceof TestClassC);

        $this->should('Throw an exception if a the class you are trying to get does not exists.');
        try {
            $this->fireDi->get('UndefinedClass');
            $this->assert(false);
        } catch (DiException $e) {
            $this->assert(true);
        }

        $this->should('Throw an exception if a circular dependency is detected.');
        try {
            $this->fireDi->get('Test\UA1Labs\Fire\TestClassAA');
            $this->assert(false);
        } catch (DiException $e) {
            $this->assert(true);
        }
    }

    public function testGetWithObject()
    {
        $this->should('Return a TestClassB object.');
        $testClassC = $this->fireDi->get('Test\UA1Labs\Fire\TestClassC');
        $dependencies = [$testClassC];
        $testClassB = $this->fireDi->getWith('Test\UA1Labs\Fire\TestClassB', $dependencies);
        $this->assert($testClassB instanceof TestClassB);

        $this->should('Prove that TestClassB has a $C variable that is TestClassC.');
        $this->assert($testClassB->C instanceof TestClassC);

        $this->should('Prove that the object cache does not contain a TestClassB');
        $objectCache = $this->fireDi->getObjectCache();
        $this->assert(!isset($objectCache['TestClassB']));

        $this->should('Throw and exception if a the class you are trying to get does not exists.');
        try {
            $this->fireDi->getWith('UndefinedClass', []);
            $this->assert(false);
        } catch (Exception $e) {
            $this->assert(true);
        }
    }

    public function testGetObjectCache()
    {
        $this->fireDi->put('TestObject', new TestClassC());
        $objectCache = $this->fireDi->getObjectCache();

        $this->should('Return an object cache array with a key "TestObject".');
        $this->assert(isset($objectCache['TestObject']));

        $this->should('Return an object cache with the object we put into it.');
        $this->assert($objectCache['TestObject'] instanceof TestClassC);
    }

    public function testClearObjectCache()
    {
        $this->should('Remove all objects from the object cache');
        $this->fireDi->put('TestObject', new TestClassC());
        $this->fireDi->clearObjectCache();
        $objectCache = $this->fireDi->getObjectCache();
        $this->assert(empty($objectCache));
    }
}

/**
 * Test classes for testing dependency injection
 */
class TestClassA
{
    public function __construct(TestClassB $B)
    {}
}

class TestClassB
{
    public $C;

    public function __construct(TestClassC $C)
    {
        $this->C = $C;
    }
}

class TestClassC
{}

class TestClassD
{
    public $A;
    public $B;

    public function __construct(TestClassA $A, TestClassB $B)
    {
        $this->A = $A;
        $this->B = $B;
    }
}

class TestClassAA {
    public function __construct(TestClassBB $BB)
    {}
}

class TestClassBB {
    public function __construct(TestClassAA $AA)
    {}
}
