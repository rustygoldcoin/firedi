<?php
/**
 * @package ulfberht
 * @author Joshua L. Johnson <josh@ua1.us>
 * @link http://ua1.us
 * @copyright Copyright 2016, Joshua L. Johnson
 * @license MIT
 */

namespace ulfberht\module;

use Exception;
use ulfberht\module\doctrine\config;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use Doctrine\Common\Cache\ArrayCache;

/**
 * The purpose of this class is to provide some abstraction to doctrine itself.
 * This object will store all doctrine entity managers.
 */
class doctrine {

    /**
     * @var array This array holds all doctrine entity managers.
     */
    private $_doctrineEntityManagers;

    /**
     * The constructor.
     */
    public function __construct() {
        $this->_doctrineEntityMangers = [];
    }

    /**
     * This method is used to add an entity manager to the doctrine service.
     *
     * @param $id string The id you would like to store with the entity manager.
     * @param ulfberht\module\doctrine\config $config The doctrine configuration for
     *        the entity manager.
     */
    public function addEntityManager($id, config $config) {
        $development = ($config->develop) ? true : false;
        $cache = ($config->enableCache) ? new ArrayCache() : null;

        //setup type of metadata reading
        switch ($config->type) {
            case 'annotation':
                $docConfig = Setup::createAnnotationMetadataConfiguration($config->paths, $development, null, $cache);
            break;
            case 'xml':
                $docConfig = Setup::createXMLMetadataConfiguration($config->paths, $development, null, $cache);
            break;
            case 'yaml':
                $docConfig = Setup::createYAMLMetadataConfiguration($config->paths, $development, null, $cache);
            break;
        }
        
        //setup caching
        if (!is_null($cache)) {
            $docConfig->setQueryCacheImpl($cache);
            $docConfig->setMetadataCacheImpl($cache);
        }

        //setup database connection
        $dbConnInfo = Array(
            'driver' =>  $config->database->driver,
            'host' =>  $config->database->host,
            'dbname' =>  $config->database->name,
            'user' =>  $config->database->user,
            'password' =>  $config->database->password
        );

        //store entity manager
        $this->_doctrineEntityMangers[$id] = EntityManager::create($dbConnInfo, $docConfig);
    }

    /**
     * This method is used to get the doctrine config for a specific entity manager.
     *
     * @param $id string The entity manager id you want the config for.
     * @return Doctrine\ORM\Configuration
     * @exception If no entity manager found for the id given.
     */
    public function getDotrineConfig($id) {
        if (!isset($this->_doctrineEntityMangers[$id])) {
            throw new Exception('Could not find entityManager "' . $id . '"');
        }
        return $this->_docConfig[$id]->getConfiguration();
    }

    /**
     * This method is used to get an instance of an entity manager.
     *
     * @param $id string The entity manager id.
     * @exception If no entity manager found for the id given.
     */
    public function getEntityManager($id) {
        if (!isset($this->_doctrineEntityMangers[$id])) {
            throw new Exception('Could not find entityManager "' . $id . '"');
        }
        return $this->_doctrineEntityMangers[$id];
    }

}
