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

namespace UA1Labs\Validations;

use \UA1Labs\Fire\Test\TestCase;
use \UA1Labs\Fire\Di;

/**
 * This test suite tests an issue found when a child of a child has a dependnecy that
 * cannot be resolved and the first child was already cached.
 *
 * @link https://github.com/ua1-labs/firedi/issues/8
 */
class Issue8TestCase extends TestCase
{
    /**
     * The FireDi
     *
     * @var \UA1Labs\Fire\Di
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

    public function testChildOfChildUnresolveableScenario()
    {
        $this->should('Not throw an UA1Labs\Fire\Di\NotFoundException when TestClassB was already cached.');
        $c = new TestClassC('UA1 Labs');
        $b = new TestClassB($c);
        $this->fireDi->set(TestClassB::class, $b);
        $a = $this->fireDi->get(TestClassA::class);
        $this->assert($a instanceof TestClassA);
    }

}

/**
 * Test classes for testing dependency injection
 */
class TestClassA
{
    public function __construct(TestClassB $B)
    {
        $this->B = $B;
    }
}

class TestClassB
{
    public function __construct(TestClassC $C)
    {
        $this->C = $C;
    }
}

class TestClassC
{
    public function __construct($name)
    {}
}