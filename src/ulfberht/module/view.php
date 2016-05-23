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

    public function render($viewPath, $viewModel = []){
        if (!file_exists($viewPath)) {
            throw new Exception('Could not find view "' . $viewPath . '".');
        }

        extract($viewModel);
        include $viewPath;
    }

}