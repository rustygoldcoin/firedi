<?php
namespace ulfberht\module\ulfberht;

use Exception;
use ulfberht\module\ulfberht\config;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

class doctrine {

    private $_config;
    
    private $_doctrineObjects;

    public function __construct(config $config) {
        $this->_doctrineObjects = [];
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
            
            $development = (isset($config['develop']) && $config['develop']) ? true : false;
            switch ($config['type']) {
                case 'annotation':
                    $docConfig = Setup::createAnnotationMetadataConfiguration($config['paths'], $development);
                break;
                case 'xml':
                    $docConfig = Setup::createXMLMetadataConfiguration($config['paths'], $development);
                break;
                case 'yaml':
                    $docConfig = Setup::createYAMLMetadataConfiguration($config['paths'], $development);
                break;
            }
            $this->_doctrineObjects[$id] = EntityManager::create($config['database'], $docConfig);
        }   
    }
    
    public function getEntityManager($id) {
        if (!isset($this->_doctrineObjects[$id])) {
            throw new Exception('Could not find entityManager "' . $id . '"');
        }
        return $this->_doctrineObjects[$id];
    }

}