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
use UA1Labs\Fire\Di\Graph;

class GraphTestCase extends TestCase
{
    /**
     * The FireDi Graph Class
     *
     * @var Graph
     */
    private $_graph;

    public function beforeEach()
    {
        $this->_graph = new Graph();
    }

    public function afterEach()
    {
        unset($this->_graph);
    }

    public function testConstructor()
    {
        $this->should('Not throw an exception when the class is constructed');
        $this->assert(true);
    }

}
