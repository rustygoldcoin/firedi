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

use \ReflectionClass;
use \UA1Labs\Fire\Di\Graph;
use \UA1Labs\Fire\Di\ClassDefinition;
use \UA1Labs\Fire\DiException;

/**
 * The Di class is what makes dependency injection possible. This class handles the
 * entire dependency injection environment.
 */
class Di
{

    public const ERROR_CLASS_NOT_FOUND = 'Class "%s" does not exist and it definition cannot be registered with FireDI.';
    public const ERROR_CIRCULAR_DEPENDENCY = 'While trying to resolve class "%s", FireDI found that there was a cirular dependency caused by the class "%s".';
    public const ERROR_DEPENDENCY_NOT_FOUND = 'While trying to resolve class "%s", FireDI found that the class dependency "%s" could not be found.';

    /**
     * A map that stores all class definitions.
     *
     * @var array<\UA1Labs\Fire\Di\ClassDefinition>
     */
    private $classDefinitions;

    /**
     * Stores objects and callables for resolving depedencies.
     *
     * @var array<mixed>
     */
    private $objectCache;

    /**
     * A dependency graph containing information about classes
     * and their dependencies.
     *
     * @var \UA1Labs\Fire\Di\Graph
     */
    private $classDependencyGraph;

    /**
     * The class constructor.
     */
    public function __construct()
    {
        $this->classDefinitions = [];
        $this->objectCache = [];
        $this->classDependencyGraph = new Graph();
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
        $this->setCachedObject($classname, $instanceObject);
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
        return $this->resolveInstanceObject($classname);
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
        if (!$this->isClassDefinitionRegistered($classname)) {
            $this->registerClassDefinition($classname);
        }

        return $this->instanciateClass($classname, $dependencies);
    }

    /**
     * Returns the entire object cache.
     *
     * @return array<mixed>
     */
    public function getObjectCache()
    {
        return $this->objectCache;
    }

    /**
     * Clears the object cache.
     *
     * @return void
     */
    public function clearObjectCache()
    {
        $this->objectCache = [];
    }

    /**
     * Determines if a object already has been cached for the given class.
     *
     * @return boolean
     */
    private function isObjectCached($classname)
    {
        return isset($this->objectCache[$classname]);
    }

    /**
     * Returns the cached object if it exists. Otherwise it returns null.
     *
     * @param string $classname The classname you want to obtain the object for.
     * @return object|null
     */
    private function getCachedObject($classname)
    {
        if ($this->isObjectCached($classname)) {
            return $this->objectCache[$classname];
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
    private function setCachedObject($classname, $object)
    {
        $this->objectCache[$classname] = $object;
    }

    /**
     * Determines if a class definition has already been registered with FireDI.
     *
     * @param string $classname
     * @return boolean
     */
    private function isClassDefinitionRegistered($classname)
    {
        return isset($this->classDefinitions[$classname]);
    }

    /**
     * Registers a class definition with FireDI.
     *
     * @param string $classname The class you would like to register
     * @throws DiException if the class does not exist
     * @return void
     */
    private function registerClassDefinition($classname)
    {
        if (!class_exists($classname)) {
            $errorMessage = sprintf(self::ERROR_CLASS_NOT_FOUND, $classname);
            throw new DiException($errorMessage);
        }

        $this->classDefinitions[$classname] = new ClassDefinition($classname);
        $this->classDependencyGraph->addResource($classname);
        $this->classDependencyGraph->addDependencies(
            $classname,
            $this->classDefinitions[$classname]->dependencies
        );
    }

    /**
     * Returns the class definition object if it has been registered. Otherwise returns null.
     *
     * @param string $classname
     * @return object|void
     */
    private function getClassDefinition($classname)
    {
        if ($this->isClassDefinitionRegistered($classname)) {
            return $this->classDefinitions[$classname];
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
    private function resolveInstanceObject($classname)
    {
        // return object from the object cache if it is there
        if ($this->isObjectCached($classname)) {
            return $this->getCachedObject($classname);
        }

        // if the class doesn't have a class definition we should attempt
        // to register the class definition
        if (!$this->isClassDefinitionRegistered($classname)) {
            $this->registerClassDefinition($classname);
        }

        // recursively register dependency class definitions
        $this->registerDependentClassDefinitions($classname);

        $classDefinition = $this->getClassDefinition($classname);
        $dependencies = $classDefinition->dependencies;

        $di = [];
        foreach ($dependencies as $dependency) {
            //set $di[] with all dependencies and invoke
            $di[] = $this->resolveInstanceObject($dependency);
        }
        return $this->instanciateClass($classname, $di, true);
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
    private function registerDependentClassDefinitions($classname)
    {
        $classDefinition = $this->getClassDefinition($classname);
        foreach ($classDefinition->dependencies as $dependency) {
            if (!$this->isClassDefinitionRegistered($dependency)) {
                $this->registerClassDefinition($dependency);
            }
            $this->circularDependencyErrorCheck($classname);
            $this->registerDependentClassDefinitions($dependency);
        }
    }

    /**
     * Validates that dependencies are able to be resolved. Also determines if there
     * are any circular dependencies.
     *
     * @param string $classname The class you are checking dependencies for
     * @throws DiException
     * @return array<string> The order we need to resolve dependencies
     */
    private function circularDependencyErrorCheck($classname)
    {
        $error = $this->classDependencyGraph->runDependencyCheck($classname);
        if ($error) {
            switch ($error->code) {
                case 2:
                    $errorMessage = sprintf(self::ERROR_CIRCULAR_DEPENDENCY, $classname, $error->resourceId);
                    throw new DiException($errorMessage);
                    break;
            }
        }

        $this->classDependencyGraph->resetDependencyCheck();
    }

    /**
     * Instanciates a class with its given resolved dependencies.
     *
     * @param string $classname The class you want to instanciate
     * @param array<mixed> $resolvedDependencies The class dependencies
     * @return void
     */
    private function instanciateClass($classname, $resolvedDependencies, $cache = false)
    {
        $classDefinition = $this->getClassDefinition($classname);
        $classDef = $classDefinition->classDef;
        if ($cache) {
            if (!$this->isObjectCached($classname)) {
                $this->setCachedObject($classname, $classDef->newInstanceArgs($resolvedDependencies));
            }
            return $this->getCachedObject($classname);
        } else {
            $classDef = $classDefinition->classDef;
            return $classDef->newInstanceArgs($resolvedDependencies);
        }
    }
}
