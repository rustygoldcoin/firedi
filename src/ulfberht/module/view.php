<?php

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