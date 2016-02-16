<?php

/**
 * @package ulfberht
 * @author Joshua L. Johnson <josh@ua1.us>
 * @link http://labs.ua1.us
 * @copyright Copyright 2016, Joshua L. Johnson
 * @license MIT
 */

namespace ulfberht\module\ulfberht;

use ulfberht\module\ulfberht\config;

class router {

    private $_config;
    private $_currentRoute;
    private $_matchedRoute;
    private $routeVars;

    public function __construct(config $config) {
        $this->_config = $config;
        $this->_config->set('routes', array());
        $this->_currentRoute = $_SERVER['REQUEST_URI'];
        $this->_matchedRoute = false;
        $this->_routeVars = array();
    }

    public function when($path, $controller) {
        $this->_setRoute($path, $controller);
        return $this;
    }

    public function otherwise($controller) {
        $this->_setRoute('default', $controller);
        return $this;
    }

    public function redirect($path) {
        if (substr($path, 0, 1) != '/') {
            $path = '/' . $path;
        }
        header('location:' . $path);
    }

    public function resolveRoute() {
        $routeConfig = $this->_config->get('routes');
        $currentRoute = $this->getCurrentRoute();
        if (array_key_exists($currentRoute, $routeConfig)) {
            $this->_matchedRoute = $currentRoute;
            return $routeConfig[$currentRoute];
        } else {
            //remove url query params and parse route into its parts
            $routeQuery = explode('?', $currentRoute);
            $routeParts = explode('/', substr($routeQuery[0], 1));
            foreach ($routeConfig as $path => $controller) {
                if (strpos($path, ':') !== false) {
                    $routeMatch = true;
                    $pathParts = explode('/', substr($path, 1));
                    $i = 0;
                    foreach ($pathParts as $part) {
                        if ($routeMatch) {
                            $routeMatch = false;
                            if (isset($routeParts[$i]) && $routeParts[$i] != '') {
                                if (strpos($part, ':') !== false) {
                                    $routeMatch = true;
                                } elseif ($part == $routeParts[$i]) {
                                    $routeMatch = true;
                                }
                            }
                            $i++;
                        }
                    }
                    if (isset($routeParts[$i])) {
                        $routeMatch = false;
                    }
                    if ($routeMatch) {
                        $this->_matchedRoute = $path;
                        $matchedRoute = explode('/', substr($this->_matchedRoute, 1));
                        $i = 0;
                        foreach ($matchedRoute as $matchedRoutePart) {
                            if (strpos($matchedRoutePart, ':') !== false) {
                                $this->routeVars[substr($matchedRoutePart, 1)] = $routeParts[$i];
                            }
                            $i++;
                        }
                        return $controller;
                    }
                }
            }
            if (array_key_exists('default', $routeConfig)) {
                $this->_matchedRoute = false;
                return $routeConfig['default'];
            } else {
                return false;
            }
        }
    }

    public function getRouteVars($routeParam = '') {
        if ($routeParam) {
            if (isset($this->routeVars[$routeParam])) {
                return $this->routeVars[$routeParam];
            }
        } else {
            return $this->routeVars;
        }
        return false;
    }

    public function getCurrentRoute() {
        return $this->_currentRoute;
    }

    public function getMatchedRoute() {
        return $this->_matchedRoute;
    }

    private function _setRoute($path, $controller) {
        $routeConfig = $this->_config->get('routes');
        $routeConfig[$path] = $controller;
        $this->_config->set('routes', $routeConfig);
    }
}