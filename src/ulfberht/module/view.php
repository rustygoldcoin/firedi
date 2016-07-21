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

class view {

    private $_layout = false;
    private $_view = false;

    public function setLayout($layoutFile) {
        if (!file_exists($layoutFile)) {
            throw new Exception('Could not find view layout "' . $layoutFile . '".');
        }

        $this->_layout = $layoutFile;
    }

    public function render($file){
        if (!file_exists($file)) {
            throw new Exception('Could not find view "' . $file . '".');
        }
        ob_start();
        include ($file);
        if (!$this->_layout) {
            ob_end_flush();
        } else {
            $this->_view = ob_get_contents();
            ob_end_clean();
            $this->_renderLayout();
        }
    }

    public function view() {
        if($this->_view) {
            echo $this->_view;
        }
    }

    private function _renderLayout() {
        ob_start();
        include ($this->_layout);
        ob_end_flush();
    }

}
