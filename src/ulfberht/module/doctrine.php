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
        $this->_config = $config->get('doctrine');
        if (!$this->_config) {
            throw new Exception('Could not find Doctrine Config.');
        }

        foreach ($this->_config as $id => $config) {
            if (!isset($config['type'])) {
                throw new Exception('Undefined parameter "type" in "' . $id . '" doctrine config.');
            }
            if (!isset($config['paths'])) {
                throw new Exception('Undefined parameter "paths" in "' . $id . '" doctrine config.');
            }
            if (!isset($config['database'])) {
                throw new Exception('Undefined parameter "database" in "' . $id . '" doctrine config.');
            }

            if (!isset($config['enableSecondLevelCache'])) {
                $config['enableSecondLevelCache'] = false;
            }

            if ($config['enableSecondLevelCache']) {
                if ($development) {
                    $cache = new \Doctrine\Common\Cache\ArrayCache;
                } else {
                    $cache = new \Doctrine\Common\Cache\ApcCache;
                }
            }

            $development = (isset($config['develop']) && $config['develop']) ? true : false;
            switch ($config['type']) {
                case 'annotation':
                    $docConfig = Setup::createAnnotationMetadataConfiguration($config['paths'], $development, null, $cache);
                break;
                case 'xml':
                    $docConfig = Setup::createXMLMetadataConfiguration($config['paths'], $development, null, $cache);
                break;
                case 'yaml':
                    $docConfig = Setup::createYAMLMetadataConfiguration($config['paths'], $development, null, $cache);
                break;
            }
            $this->_docConfig[$id] = $docConfig;
            if (isset($cache)) {
                $docConfig->setQueryCacheImpl($cache);
                $docConfig->setMetadataCacheImpl($cache);
            }
            $this->_doctrineObjects[$id] = EntityManager::create($config['database'], $docConfig);
        }
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
