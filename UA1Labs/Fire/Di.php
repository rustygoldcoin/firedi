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

namespace UA1Labs\Fire;

use ReflectionClass;
use UA1Labs\Fire\Di\Graph;
use UA1Labs\Fire\Di\ClassDefinition;
use UA1Labs\Fire\DiException;

/**
 * The Di class is what makes dependency injection possible. This class handles the
 * entire dependency injection environment.
 */
class Di
{

    const ERROR_CLASS_NOT_FOUND = 'Class "%s" does not exist and it definition cannot'
        . ' be registered with FireDI.';
    const ERROR_CIRCULAR_DEPENDENCY = 'While trying to resolve class "%s",'
        . ' FireDI found that there was a cirular dependency caused by the class'
        . ' "%s".';
    const ERROR_DEPENDENCY_NOT_FOUND = 'While trying to resolve class "%s",'
        . ' FireDI found that the class dependency "%s"'
        . ' could not be found.';

    /**
     * A map that stores all class definitions.
     *
     * @var array<UA1Labs\Fire\Di\ClassDefinition>
     */
    private $_classDefinitions;

    /**
     * Stores singleton object that have been registered as singleton type class.
     *
     * @var array
     */
    private $_objectCache;

    /**
     * A dependency graph containing information about classes
     * and their dependencies.
     *
     * @var UA1Labs\Fire\Di\Graph
     */
    private $_classDependencyGraph;

    /**
     * The class constructor.
     */
    public function __construct()
    {
        $this->_classDefinitions = [];
        $this->_objectCache = [];
        $this->_classDependencyGraph = new Graph();
    }

    /**
     * Puts an object into the object cache that is used to resolve dependencies.
     *
     * @param string $classname The classname the instance object should resolve for
     * @param object $instanceObject The instance object you want to return for the classname
     * @return void
     */
    public function put($classname, $instanceObject)
    {
        $this->_setCachedObject($classname, $instanceObject);
    }

    /**
     * Attempts to retrieve an instance object of the given classname by resolving its
     * dependencies and creating an instance of the object.
     *
     * @param $classname string The class you would like to instanciate
     * @return object The instanciated object based on the $classname
     */
    public function get($classname)
    {
        return $this->_resolveInstanceObject($classname);
    }

    /**
     * Returns an instance object for the given classname and dependencies.
     *
     * @param string $classname
     * @param array<object> $dependencies
     * @return object
     */
    public function getWith($classname, $dependencies)
    {
        // if the class doesn't have a class definition we should attempt
        // to register the class definition
        if (!$this->_isClassDefinitionRegistered($classname)) {
            $this->_registerClassDefinition($classname);
        }

        return $this->_instanciateClass($classname, $dependencies);
    }

    /**
     * Returns the entire object cache.
     *
     * @return array<mixed>
     */
    public function getObjectCache()
    {
        return $this->_objectCache;
    }

    /**
     * Clears the object cache.
     *
     * @return void
     */
    public function clearObjectCache()
    {
        $this->_objectCache = [];
    }

    /**
     * Determines if a object already has been cached for the given class.
     *
     * @return boolean
     */
    private function _isObjectCached($classname)
    {
        return isset($this->_objectCache[$classname]);
    }

    /**
     * Returns the cached object if it exists. Otherwise it returns null.
     *
     * @param string $classname The classname you want to obtain the object for.
     * @return object|null
     */
    private function _getCachedObject($classname)
    {
        if ($this->_isObjectCached($classname)) {
            return $this->_objectCache[$classname];
        }

        return null;
    }

    /**
     * Sets the object in object cache.
     *
     * @param string $classname The class you want to set the object cache for
     * @param object $object The object you want to set the object cache for
     * @return void
     */
    private function _setCachedObject($classname, $object)
    {
        $this->_objectCache[$classname] = $object;
    }

    /**
     * Determines if a class definition has already been registered with FireDI.
     *
     * @param string $classname
     * @return boolean
     */
    private function _isClassDefinitionRegistered($classname)
    {
        return isset($this->_classDefinitions[$classname]);
    }

    /**
     * Registers a class definition with FireDI.
     *
     * @param string $classname The class you would like to register
     * @return void
     */
    private function _registerClassDefinition($classname)
    {
        if (!class_exists($classname)) {
            $errorMessage = sprintf(self::ERROR_CLASS_NOT_FOUND, $classname);
            throw new DiException($errorMessage);
        }

        $this->_classDefinitions[$classname] = new ClassDefinition($classname);
        $this->_classDependencyGraph->addResource($classname);
        $this->_classDependencyGraph->addDependencies(
            $classname,
            $this->_classDefinitions[$classname]->dependencies
        );
    }

    /**
     * Returns the class definition object if it has been registered. Otherwise returns null.
     *
     * @param string $classname
     * @return object|void
     */
    private function _getClassDefinition($classname)
    {
        if ($this->_isClassDefinitionRegistered($classname)) {
            return $this->_classDefinitions[$classname];
        }

        return null;
    }

    /**
     * This method is used to resolve an instance object and all of its dependencies. When
     * resolving a dependency, this method will run recursively to resolve all dependencies
     * in the dependency tree.
     *
     * @param $classname The classname of the instance object you would like to resolve
     * @return mixed The resolved instance object
     */
    private function _resolveInstanceObject($classname)
    {
        // return object from the object cache if it is there
        if ($this->_isObjectCached($classname)) {
            return $this->_getCachedObject($classname);
        }

        // if the class doesn't have a class definition we should attempt
        // to register the class definition
        if (!$this->_isClassDefinitionRegistered($classname)) {
            $this->_registerClassDefinition($classname);
        }

        // recursively register dependency class definitions
        $this->_registerDependentClassDefinitions($classname);

        $classDefinition = $this->_getClassDefinition($classname);
        $dependencies = $classDefinition->dependencies;

        $di = [];
        foreach ($dependencies as $dependency) {
            //set $di[] with all dependencies and invoke
            $di[] = $this->_resolveInstanceObject($dependency);
        }
        return $this->_instanciateClass($classname, $di, true);
    }

    /**
     * Runs through a class's dependencies and ensures that each dependency
     * has been registered as both a class definition and a resource in the
     * dependency graph. As a note, this is a recursive function and we do a
     * circular dependency error check before we start to register any of the
     * current class's depenencies to ensure we don't end up in an infinite loop.
     *
     * @param string $classname The class you would like to register the dependencies for.
     * @return void
     */
    private function _registerDependentClassDefinitions($classname)
    {
        $classDefinition = $this->_getClassDefinition($classname);
        foreach ($classDefinition->dependencies as $dependency) {
            if (!$this->_isClassDefinitionRegistered($dependency)) {
                $this->_registerClassDefinition($dependency);
            }
            $this->_circularDependencyErrorCheck($classname);
            $this->_registerDependentClassDefinitions($dependency);
        }
    }

    /**
     * Validates that dependencies are able to be resolved. Also determines if there
     * are any circular dependencies.
     *
     * @param string $classname The class you are checking dependencies for
     * @return array<string> The order we need to resolve dependencies
     */
    private function _circularDependencyErrorCheck($classname)
    {
        $error = $this->_classDependencyGraph->runDependencyCheck($classname);
        if ($error) {
            switch ($error->code) {
                case 2:
                    $errorMessage = sprintf(self::ERROR_CIRCULAR_DEPENDENCY, $classname, $error->resourceId);
                    throw new DiException($errorMessage);
                    break;
            }
        }

        $this->_classDependencyGraph->resetDepenencyCheck();
    }

    /**
     * Instanciates a class with its given resolved dependencies.
     *
     * @param string $classname The class you want to instanciate
     * @param array<mixed> $resolvedDependencies The class dependencies
     * @return void
     */
    private function _instanciateClass($classname, $resolvedDependencies, $cache = false)
    {
        $classDefinition = $this->_getClassDefinition($classname);
        $classDef = $classDefinition->classDef;
        if ($cache) {
            if (!$this->_isObjectCached($classname)) {
                $this->_setCachedObject($classname, $classDef->newInstanceArgs($resolvedDependencies));
            }
            return $this->_getCachedObject($classname);
        } else {
            $classDef = $classDefinition->classDef;
            return $classDef->newInstanceArgs($resolvedDependencies);
        }
    }
}
