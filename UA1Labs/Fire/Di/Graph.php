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

namespace UA1Labs\Fire\Di;

use stdClass;

/**
 * This class makes it to easy to manage a dependency network
 * by allowing you to run dependency checks on all of the resources you have
 * available in your network.
 */
class Graph
{

    /**
     * This array is filled with dependencies that have been resolved during the
     * dependency check that is done when you run ::runDependencyCheck()
     *
     * @var array<string>
     */
    private $_resolved;

    /**
     * This array is filled with dependencies that have yet to be resolved during
     * the dependency check that is done when you run ::runDependencyCheck().
     *
     * @var array<string>
     */
    private $_unresolved;

    /**
     * This array contains a list of resources and dependencies and is modeled
     * using a graph technique.
     *
     * @var array<string>
     */
    private $_resourceGraph;

    /**
     * Contains an error code configuration for determining which
     * error codes would be sent back to the client using this class.
     *
     * @var array<object>
     */
    private $_errorCodes;

    /**
     * The class constructor.
     */
    public function __construct()
    {
        $this->_resolved = [];
        $this->_unresolved = [];
        $this->_resourceGraph = [];
        $this->_dependencyError = false;
        $this->_errorConfig();
    }

    /**
     * This method is used to add a resource to the dependecy graph.
     *
     * @param string $resourse_id A unique ID that identifies a resource
     * @return UA1Labs\Fire\Di\Graph
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
     *     are targeting.
     * @return boolean
     */
    public function isResource($resource)
    {
        return isset($this->_resourceGraph[$resource]);
    }

    /**
     * This method is used to add multiple dependencies to a resource.
     *
     * @param string $resourceId A unique ID that repesents the resource you
     *     want to add dependencies to.
     * @param array $dependencies An array that contains the dependencies you want
     *     to apply to the resource.
     * @return UA1Labs\Fire\Di\Graph
     */
    public function addDependencies($resourceId, array $dependencies)
    {
        $this->_resourceGraph[$resourceId] = array_merge($this->_resourceGraph[$resourceId], $dependencies);
        return $this;
    }

    /**
     * This method is used to add a single dependency to a resource.
     *
     * @param string $resourceId A unique ID that repesents the resource you
     *    want to add dependency to.
     * @param string $dependency A var that contains the dependency you want
     *    to apply to the resource.
     * @return UA1Labs\Fire\Di\Graph
     */
    public function addDependency($resourceId, $dependency)
    {
        $this->_resourceGraph[$resourceId][] = $dependency;
        return $this;
    }

    /**
     * This method will return an array of the dependencies a resource has.
     *
     * @param string $resourceId A unique ID of the resource you would like to
     *    get the dependencies of
     * @return array<string> The dependencies of the resource.
     */
    public function getDependencies($resourceId)
    {
        return $this->_resourceGraph[$resourceId];
    }

    /**
     * This method will take the $resource and run an algorithm that will check
     * for a circular dependency situation or a null dependency. If there are
     * any errors, this method will not stop processing. To check for errors,
     * you will need to run the self::getDependencyError() method. To reset
     * the dependency check use the self::resetDependencyCheck() method.
     *
     * @param string $resource The unique ID of the resource you would like to
     *    run the dependency check on.
     * @return object|void Will return an error code object if an error was found.
     *    Void if no error.
     */
    public function runDependencyCheck($resourceId)
    {
        if ($resourceExistsError = $this->_runResourceExistsCheck($resourceId)) {
            return $resourceExistsError;
        }

        if ($circularDepError = $this->_runCircularDependencyCheck($resourceId)) {
            return $circularDepError;
        }
    }

    /**
     * This method will return an array that contains all of the dependencies
     * that have to be resolved in order to achieve proper dependency resolution.
     * The contents of the array have been ordered by which depedencies should
     * be resolved first.
     *
     * @return array This array will contain the order in which the dependencies
     *     should be resolved in order to achieve proper dependency resolutions.
     */
    public function getDependencyResolveOrder()
    {
        $dependencies = $this->_resolved;
        array_pop($dependencies);
        return $dependencies;
    }

    /**
     * This method resets the status of all properies required to run another
     * dependency check.
     *
     * @return void
     */
    public function resetDepenencyCheck()
    {
        $this->_resolved = [];
        $this->_unresolved = [];
    }

    /**
     * This method is a helper method to set the default error config values.
     *
     * @return void
     */
    private function _errorConfig()
    {
        //error code config
        $error1 = (object) [
            'code' => 1,
            'resourceId' => false,
            'description' => 'Resource Not Found'
        ];

        $error2 = (object) [
            'code' => 2,
            'resourceId' => false,
            'description' => 'Circular Dependency Detected'
        ];

        $this->_errorCodes = [
            1 => $error1,
            2 => $error2
        ];
    }

    /**
     * This method returns an error object based on the code sets the resourceId.
     *
     * @param $code interger The error code you would like the associate with the error.
     * @param $resourceId string The resourceId identified that caused the error.
     * @return object The error object that represents the runtime error.
     */
    private function _getError($code, $resourceId = false)
    {
        $error = $this->_errorCodes[$code];
        $error->resourceId = $resourceId;
        $this->resetDepenencyCheck();
        return $error;
    }

    /**
     * This method is used to check to see if a resource exist in the graph object.
     *
     * @param resourceId The resourceId you would like to check.
     * @return object|void Void if resourceId is found. Error object if resource not found.
     */
    private function _runResourceExistsCheck($resourceId)
    {
        if (!$this->isResource($resourceId)) {
            return $this->_getError(1, $resourceId);
        }
    }

    /**
     * This method checks a resource and its dependencies for any situations where
     * there is a case that causes the dependnecy to be not resolvable.
     *
     * @param string The resource you would like to run the check for.
     * @return object|void Void if no error. Error object if it finds an error.
     */
    private function _runCircularDependencyCheck($resourceId)
    {
        $this->_unresolved[$resourceId] = $resourceId;
        foreach ($this->_resourceGraph[$resourceId] as $dependency) {
            if (!in_array($dependency, $this->_resolved)) {
                if (in_array($dependency, $this->_unresolved)) {
                    return $this->_getError(2, $resourceId);
                }
                //return recursive error if any
                if ($error = $this->runDependencyCheck($dependency)) {
                    return $error;
                }
            }
        }
        unset($this->_unresolved[$resourceId]);
        $this->_resolved[$resourceId] = $resourceId;
    }
}
