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

namespace Test\UA1Labs\Fire\Di;

use Fire\Test\TestCase;
use UA1Labs\Fire\Di\ClassDefinition;

class ClassDefinitionTestCase extends TestCase
{
    /**
     * The FireDi ClassDefinition Class
     *
     * @var ClassDefinition
     */
    private $_classDefinition;

    public function beforeEach()
    {
        $this->_classDefinition = new ClassDefinition('stdClass');
    }

    public function afterEach()
    {
        unset($this->_classDefinition);
    }

    public function testConstructor()
    {
        $this->should('Not throw an exception when the class is constructed');
        $this->assert(true);
    }

}
