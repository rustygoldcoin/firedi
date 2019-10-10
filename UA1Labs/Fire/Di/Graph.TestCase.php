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
use Throwable;

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

    public function testAddResource()
    {
        $this->should('Add a resource to the resource graph.');
        $this->_graph->addResource('Resource');
        $this->assert($this->_graph->isResource('Resource'));
    }

    public function testAddDependencies()
    {
        $this->should('Add dependencies to a resource.');
        $dependencies = ['Dependency1', 'Dependency2'];
        $this->_graph->addResource('MyResource');
        $this->_graph->addDependencies('MyResource', $dependencies);
        $this->assert($this->_graph->getDependencies('MyResource') === $dependencies);

        $this->should('Throw an exception if a resource does not exist to add dependencies to.');
        try {
            $this->_graph->addResouce('UnknownResource', ['DependentResource']);
            $this->assert(false);
        } catch (Throwable $e) {
            $this->assert(true);
        }
    }

    public function testAddDependency()
    {
        $this->should('Add a dependency to a resource.');
        $this->_graph->addResource('MyResouce');
        $this->_graph->addDependency('MyResource', 'Dependency1');
        $this->assert($this->_graph->getDependencies('MyResource') === ['Dependency1']);

        $this->should('Throw an exception if a resource does not exist to add dependencies to.');
        try {
            $this->_graph->addResouce('UnknownResource', 'DependentResource');
            $this->assert(false);
        } catch (Throwable $e) {
            $this->assert(true);
        }
    }

    public function testRunDependencyCheck()
    {

    }

    public function testGetDependencyResolveOrder()
    {

    }

    public function testResetDependencyCheck()
    {

    }

}
