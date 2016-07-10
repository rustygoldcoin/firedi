<?php
/**
 * @package ulfberht
 * @author Joshua L. Johnson <josh@ua1.us>
 * @link http://ua1.us
 * @copyright Copyright 2016, Joshua L. Johnson
 * @license MIT
 */

namespace ulfberht\module;

/**
 * This method is to provide a data storage solution to carry different
 * things that may need to be stored. It is a key=value type store.
 */
class store {

    /**
     * @var array The array all things are stored in.
     */
    private $_store;

    /**
     * The constructor.
     */
    public function __construct() {
        $this->_store = [];
    }

    /**
     * This method is to store values.
     *
     * @param $key string The key you want to store the value for.
     * @param $value string The value you want to store.
     */
    public function set($key, $value) {
        $this->_store[$key] = $value;
    }

    /**
     * This method is used to retrieve data form the store.
     *
     * @param $key string The key you would like to get the data for.
     * @return mixed | null Value if set, Null if not.
     */
    public function get($key) {
        if(isset($this->_store[$key])) {
            return $this->_store[$key];
        }
        return null;
    }

}