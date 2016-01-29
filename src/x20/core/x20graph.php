<?php

/**
 * @package x20
 * @author Joshua L. Johnson <josh@ua1.us>
 * @link http://labs.ua1.us
 * @copyright Copyright 2016, Joshua L. Johnson
 * @license MIT
 */

namespace z20\core;

/**
 * The x20graph class makes it to easy to manage a dependency network
 * by allowing you to run dependency checks on all of the resources you have
 * available in your network.
 */
class x20graph
{

    /**
     * This array is filled with dependencies that have resolved during the
     * mock dependency check that is done when you run x20graph::runDependencyCheck()
     *
     * @var array
     */
    protected $_resolved;

    /**
     * This array is filled with dependencies that have yet to be resolved during
     * the mock dependency check that is done when you run x20graph::runDependencyCheck().
     *
     * @var array
     */
    protected $_unresolved;

    /**
     * This array contains a list of resources and dependencies and is modeled
     * using a graph technique.
     *
     * @var array
     */
    protected $_resourceGraph;

    /**
     * This var is used to determine if an error had been produced during
     * the mock dependency check when you run x20graph::runDependencyCheck().
     *
     * @var boolean
     */
    protected $_dependencyError;

    /**
     * The constructor
     */
    public function __construct()
    {
        $this->_resolved = [];
        $this->_unresolved = [];
        $this->_resourceGraph = [];
        $this->_dependencyError = false;
    }

    /**
     * This method is used to add a resource to the dependecy graph.
     *
     * @param string $resourse_id A unique ID that identifies a resource
     * @return x20graph
     */
    public function addResource($resourse_id)
    {
        $this->_resourceGraph[$resourse_id] = [];
        return $this;
    }

    /**
     * This method is used to determine if a resourse exists within the
     * dependency graph.
     *
     * @param string $resource The unique ID that identifies the resource you
     * are targeting.
     * @return boolean
     */
    public function isResource($resource)
    {
        if (isset($this->_resourceGraph[$resource])) {
            return true;
        }

        return false;
    }

    /**
     * This method is used to add multiple dependencies to a resource.
     *
     * @param string $resource_id A unique ID that repesents the resource you
     * want to add dependencies to.
     * @param array $dependencies An array that contains the dependencies you want
     * to apply to the resource.
     * @return x20graph
     */
    public function addDependencies($resource_id, array $dependencies)
    {
        $this->_resourceGraph[$resource_id] = array_merge($this->_resourceGraph[$resource_id], $dependencies);
        return $this;
    }

    /**
     * This method is used to add a single dependency to a resource.
     *
     * @param string $resource_id A unique ID that repesents the resource you
     * want to add dependency to.
     * @param string $dependency A var that contains the dependency you want
     * to apply to the resource.
     * @return dependencyGraph
     */
    public function addDependency($resource_id, $dependency)
    {
        $this->_resourceGraph[$resource_id][] = $dependency;
        return $this;
    }

    /**
     * This method will return an array of the dependencies a resource has.
     *
     * @param string $resource_id A unique ID of the resource you would like to
     * get the dependencies of
     * @return array The dependencies of the resource.
     */
    public function getDependencies($resource_id)
    {
        return $this->_resourceGraph[$resource_id];
    }

    /**
     * This method will take the $resource and run an algorithm that will check
     * for a circular dependency situation or a null dependency. If there are
     * any errors, this method will not stop processing. To check for errors,
     * you will need to run the self::getDependencyError() method. To reset
     * the dependency check use the self::resetDependencyCheck() method.
     *
     * @param string $resource The unique ID of the resource you would like to
     * run the dependency check on.
     */
    public function runDependencyCheck($resource_id)
    {
        if (!isset($this->_resourceGraph[$resource_id])) {
            $this->_dependencyError = array(
                'code' => 1,
                'resource' => $resource_id
            );
        } else {
            $this->_unresolved[$resource_id] = $resource_id;
            foreach ($this->_resourceGraph[$resource_id] as $dependency) {
                if (!in_array($dependency, $this->_resolved)) {
                    if (in_array($dependency, $this->_unresolved)) {
                        $this->_dependencyError = array(
                            'code' => 2,
                            'resource' => $resource_id
                        );
                    }
                    if (!$this->_dependencyError) {
                        $this->runDependencyCheck($dependency);
                    }
                }
            }
            unset($this->_unresolved[$resource_id]);
            $this->_resolved[$resource_id] = $resource_id;
        }
    }

    /**
     * This method will return an array with error information if an error
     * exists on the latest run dependency check.
     *
     * @return mixed Contains an error if one exists from the
     * x20graph::runDependencyCheck() method.
     */
    public function getDependencyError()
    {
        return $this->_dependencyError;
    }

    /**
     * This method will return an array that contains all of the dependencies
     * that have to be resolved in order to achieve proper dependency resolution.
     * The contents of the array have been ordered by which depedencies should
     * be resolved first.
     *
     * @return array This array will contain the order in which the dependencies
     * should be resolved in order to achieve proper dependency resolutions.
     */
    public function getDependencyResolveOrder()
    {
        return $this->_resolved;
    }

    /**
     * This method resets the status of all properies required to run another
     * dependency check.
     */
    public function resetDepenencyCheck()
    {
        $this->_resolved = array();
        $this->_unresolved = array();
        $this->_dependencyError = false;
    }

}
