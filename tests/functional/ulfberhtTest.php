<?php
/**
 * @package ulfberht
 * @author Joshua L. Johnson <josh@ua1.us>
 * @link http://ua1.us
 * @copyright Copyright 2016, Joshua L. Johnson
 * @license MIT
 */


use PHPUnit\Framework\TestCase;
use ulfberht\core\ulfberht;
use ulfberht\core\ulfberhtException;


/**
 * Classes Used to mock services for testing.
 */
class ServiceMock1 {}
class ServiceMock2 {
    public function __construct(ServiceMock1 $mock1, ServiceMock3 $mock3) {
        $this->ServiceMock1 = $mock1;
        $this->ServiceMock3 = $mock3;
    }
}
class ServiceMock3 {
    public function __construct(ServiceMock1 $mock1) {
        $this->ServiceMock1 = $mock1;
    }
}
class ServiceMockMissingDependency {
    public function __construct(stdClass $s) {}
}
class ServiceMockCircularDependency1 {
    public function __construct(ServiceMockCircularDependency2 $s) {}
}
class ServiceMockCircularDependency2 {
    public function __construct(ServiceMockCircularDependency1 $s) {}
}

/**
 * Ulfberht Functional Test Class
 */
class ulfberhtTest extends TestCase {

    /**
     * Ulfberht object.
     * @var ulfberht\core\ulfberht
     */
    public $ulfberht;

    /**
     * Sets up Ulfberht for before each test.
     * @return void
     */
    public function setUp() {
        $this->ulfberht = new ulfberht();
    }

    /**
     * Resets Ulfbert after each test.
     * @return void
     */
    public function tearDown() {
        //ulfberht()->destroy();
    }

    /**
     * Tests when user goes to register a singleton and the class they want
     * to register does not exist.
     * @return void
     */
    public function testRegisterSingletonClassUndefined() {
        try {
            $this->ulfberht->singleton('NoClass');
        } catch (ulfberhtException $e) {
            $expectedMessage = sprintf(ulfberht::ERROR_CLASS_NOT_FOUND, 'NoClass');
            $message = $e->getMessage();
            $this->assertEquals($message, $expectedMessage);
        }
    }

    /**
     * Tests when user goes to register a singleton, but the class was already
     * registered.
     * @return void
     */
    public function testRegisterSingletonClassExists() {
        try {
            $this->ulfberht->singleton('ServiceMock1');
            $this->ulfberht->singleton('ServiceMock1');
        } catch (ulfberhtException $e) {
            $expectedMessage = sprintf(ulfberht::ERROR_CLASS_ALREADY_DEFINED, 'ServiceMock1');
            $message = $e->getMessage();
            $this->assertEquals($message, $expectedMessage);
        }
    }

    /**
     * Tests when user goes to register a factory and the class they want
     * to register does not exist.
     * @return void
     */
    public function testRegisterFactoryClassUndefined() {
        try {
            $this->ulfberht->factory('NoClass');
        } catch (ulfberhtException $e) {
            $expectedMessage = sprintf(ulfberht::ERROR_CLASS_NOT_FOUND, 'NoClass');
            $message = $e->getMessage();
            $this->assertEquals($message, $expectedMessage);
        }
    }

    /**
     * Tests when user goes to register a factory, but the class was already
     * registered.
     * @return void
     */
    public function testRegisterFactoryClassExists() {
        try {
            $this->ulfberht->factory('ServiceMock1');
            $this->ulfberht->factory('ServiceMock1');
        } catch (ulfberhtException $e) {
            $expectedMessage = sprintf(ulfberht::ERROR_CLASS_ALREADY_DEFINED, 'ServiceMock1');
            $message = $e->getMessage();
            $this->assertEquals($message, $expectedMessage);
        }
    }

    /**
     * Tests when a user goes to get a service that hasn't been registered.
     * @return void
     */
    public function testGetServiceNotFound() {
        try {
            $this->ulfberht->get('NoService');
        } catch (ulfberhtException $e) {
            $expectedMessage = sprintf(ulfberht::ERROR_SERVICE_NOT_FOUND, 'NoService');
            $message = $e->getMessage();
            $this->assertEquals($message, $expectedMessage);
        }
    }

    /**
     * Tests when a user goes to get a service that has a dependency that hasn't
     * been registered.
     * @return void
     */
    public function testGetServiceMissingDependency() {
        try {
            $this->ulfberht->factory('ServiceMockMissingDependency');
            $this->ulfberht->get('ServiceMockMissingDependency');
        } catch (ulfberhtException $e) {
            $expectedMessage = sprintf(
                ulfberht::ERROR_DEPENDENCY_NOT_FOUND,
                'ServiceMockMissingDependency',
                'stdClass'
            );
            $message = $e->getMessage();
            $this->assertEquals($message, $expectedMessage);
        }
    }

    /**
     * Tests when a users goes to get a service that has is part of a circular
     * dependency ring.
     * @return void
     */
    public function testGetServiceCircularDependency() {
        try {
            $this->ulfberht->factory('ServiceMockCircularDependency1');
            $this->ulfberht->factory('ServiceMockCircularDependency2');
            $this->ulfberht->get('ServiceMockCircularDependency1');
        } catch (ulfberhtException $e) {
            $expectedMessage = sprintf(
                ulfberht::ERROR_CIRCULAR_DEPENDENCY,
                'ServiceMockCircularDependency1',
                'ServiceMockCircularDependency2'
            );
            $message = $e->getMessage();
            $this->assertEquals($message, $expectedMessage);
        }
    }

    /**
     * Validates that when a user registers a service as a singleton it will always
     * be returned as a singleton instanciated class.
     * @return void
     */
    public function testGetServiceAsTrueSingleton() {
        $this->ulfberht->singleton('ServiceMock1');
        $singleton = $this->ulfberht->get('ServiceMock1');
        $singleton->test = true;
        $singleton1 = $this->ulfberht->get('ServiceMock1');
        $this->assertEquals(isset($singleton1->test), true);
    }

    /**
     * Validates that when a user registers a service as a factory it will always
     * return a new instance of the service requested.
     * @return void
     */
    public function testGetServiceAsTrueFactory() {
        $this->ulfberht->factory('ServiceMock1');
        $singleton = $this->ulfberht->get('ServiceMock1');
        $singleton->test = true;
        $singleton1 = $this->ulfberht->get('ServiceMock1');
        $this->assertEquals(isset($singleton1->test), false);
    }

    /**
     * Validates that when a service has been registered the ::has() method
     * returns true.
     * @return void
     */
    public function testHasServiceTrue() {
        $this->ulfberht->factory('ServiceMock1');
        $hasService = $this->ulfberht->has('ServiceMock1');
        $this->assertEquals($hasService, true);
    }

    /**
     * Validates that when a service has not been registered the ::has() method
     * returns false.
     * @return void
     */
    public function testHasServiceFalse() {
        $hasService = $this->ulfberht->has('ServiceMock1');
        $this->assertEquals($hasService, false);
    }

    /**
     * Validates when there is a complex dependency chain, all depenencies are
     * resolved correctly.
     * @return void
     */
    public function testResolveComplexService() {
        $this->ulfberht->factory('ServiceMock1');
        $this->ulfberht->factory('ServiceMock2');
        $this->ulfberht->factory('ServiceMock3');

        $service1 = $this->ulfberht->get('ServiceMock1');
        $service2 = $this->ulfberht->get('ServiceMock2');
        $service3 = $this->ulfberht->get('ServiceMock2');
        $this->assertEquals($service2->ServiceMock1 instanceof ServiceMock1, true);
        $this->assertEquals($service2->ServiceMock3 instanceof ServiceMock3, true);
        $this->assertEquals(
            $service3->ServiceMock1 instanceof ServiceMock1,
            true
        );
    }

}
