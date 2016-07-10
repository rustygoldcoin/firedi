<?php
/**
 * @package ulfberht
 * @author Joshua L. Johnson <josh@ua1.us>
 * @link http://ua1.us
 * @copyright Copyright 2016, Joshua L. Johnson
 * @license MIT
 */

namespace ulfberht\module\doctrine;

/**
 * This class is used to create a standard interface for the
 * ulfberht\module\doctrine::addEntityManager() method.
 */
class config {

    /*
     * @var The type of metadata annotation reading
     * used by doctrine.
     */
    const METADATA_TYPE_ANNOTATION = 'annotation';
    const METADATA_TYPE_XML = 'xml';
    const METADATA_TYPE_YAML = 'yaml';

    /**
     * The Constructor
     */
    public function __construct() {
        $this->develop = true;
        $this->enableCache = false;
        $this->type = self::METADATA_TYPE_ANNOTATION;
        $this->paths = [];
        $this->database = (object) [
            'driver' => 'pdo_mysql',
            'host' => '',
            'name' => '',
            'user' => '',
            'password' => ''
        ];
    }

}