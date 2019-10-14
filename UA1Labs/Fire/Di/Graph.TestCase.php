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

use UA1Labs\Fire\Test\TestCase;
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
    }

    public function testAddDependency()
    {
        $this->should('Add a dependency to a resource.');
        $this->_graph->addResource('MyResource');
        $this->_graph->addDependency('MyResource', 'Dependency1');
        $this->assert($this->_graph->getDependencies('MyResource') === ['Dependency1']);
    }

    public function testRunDependencyCheck()
    {
        $this->should('Return an error code of "1" (Resourece Not Found).');
        $this->_graph->addResource('Resource1');
        $this->_graph->addDependency('Resource1', 'Resource2');
        $check = $this->_graph->runDependencyCheck('Resource1');
        $this->assert(isset($check->code) && $check->code === 1);

        $this->should('Contain which resource was missing.');
        $this->assert(isset($check->resourceId) && $check->resourceId === 'Resource2');

        $this->_graph->resetDependencyCheck();

        $this->should('Return and error code "2" (Circular Dependnecy).');
        $this->_graph->addResource('Resource2');
        $this->_graph->addDependency('Resource2', 'Resource1');
        $check = $this->_graph->runDependencyCheck('Resource1');
        $this->assert(isset($check->code) && $check->code === 2);

        $this->should('Contain the resource of which caused the circular dependency.');
        $this->assert(isset($check->resourceId) && $check->resourceId === 'Resource2');
    }

    public function testGetDependencyResolveOrder()
    {
        $this->should('Resolve dependencies in the order they need to be resolved.');
        $this->_graph->addResource('Resource1');
        $this->_graph->addResource('Resource2');
        $this->_graph->addResource('Resource3');
        $this->_graph->addDependencies('Resource1', ['Resource2', 'Resource3']);
        $this->_graph->addDependency('Resource2', 'Resource3');
        $this->_graph->runDependencyCheck('Resource1');
        $resolveOrder = $this->_graph->getDependencyResolveOrder();
        $this->assert($resolveOrder === ['Resource3', 'Resource2']);
    }

    public function testResetDependencyCheck()
    {
        $this->should('Reset a dependency check.');
        $this->_graph->addResource('Resource1');
        $this->_graph->addResource('Resource2');
        $this->_graph->addDependency('Resouce1', 'Resource2');
        $this->_graph->runDependencyCheck('Resouce1');
        $this->_graph->resetDependencyCheck();
        $resolveOrder = $this->_graph->getDependencyResolveOrder('Resource1');
        $this->assert(empty($resolveOrder));
    }

}
