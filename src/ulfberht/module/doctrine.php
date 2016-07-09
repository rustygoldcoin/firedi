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
use ulfberht\module\config;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

class doctrine {

    private $_config;
    private $_docConfig = [];

    private $_doctrineObjects = [];

    public function __construct(config $config) {
        $id = 'application';
        $this->_config = $config->get('application')->doctrine;

        if (!$this->_config) {
            throw new Exception('Could not find Doctrine Config.');
        }

        $development = (isset($this->_config['develop']) && $this->_config['develop']) ? true : false;

        if ($this->_config['enableCache']) {
            $cache = new \Doctrine\Common\Cache\ArrayCache;
        } else {
            $cache = null;
        }

        switch ($this->_config['type']) {
            case 'annotation':
                $docConfig = Setup::createAnnotationMetadataConfiguration($this->_config['paths'], $development, null, $cache);
            break;
            case 'xml':
                $docConfig = Setup::createXMLMetadataConfiguration($this->_config['paths'], $development, null, $cache);
            break;
            case 'yaml':
                $docConfig = Setup::createYAMLMetadataConfiguration($this->_config['paths'], $development, null, $cache);
            break;
        }
        $this->_docConfig[$id] = $docConfig;
        if (!is_null($cache)) {
            $docConfig->setQueryCacheImpl($cache);
            $docConfig->setMetadataCacheImpl($cache);
        }

        $dbConnInfo = Array(
            'driver'     =>  'pdo_mysql',
            'host'       =>  $config->get('environment')->docker->database->host,
            'dbname'       =>  $config->get('environment')->docker->database->name,
            'user'       =>  $config->get('environment')->docker->database->user,
            'password'   =>  $config->get('environment')->docker->database->password
        );

        $this->_doctrineObjects[$id] = EntityManager::create($dbConnInfo, $docConfig);
    }

    public function getDotrineConfig($id) {
        if (!isset($this->_docConfig)) {
            throw new Exception('Could not find doctrine config object for "' .$id .'"');
        }
        return $this->_docConfig[$id];
    }

    public function getEntityManager($id) {

        if (!isset($this->_doctrineObjects[$id])) {
            throw new Exception('Could not find entityManager "' . $id . '"');
        }
        return $this->_doctrineObjects[$id];
    }

}
