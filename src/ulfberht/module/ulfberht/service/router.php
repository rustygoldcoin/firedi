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
    private $_rootPath;
    private $_currentRoute;
    private $_matchedRoute;
    private $routeVars;
    
    public function __construct(config $config) {
        $this->_config = $config;
        $this->_config->set('routes', array());
        $this->_rootPath = '';
        $this->_currentRoute = $_SERVER['REQUEST_URI'];
        $this->_matchedRoute = false;
        $this->_routeVars = array();
    }
    
    /**
     * This method is used to register the base URL of your site.
     *
     * @param string $baseUrl You base URL Ex: http://example.com
     */
    public function base($baseUrl) {
        //sets the base url
        $this->_config->set('baseUrl', $baseUrl);
    }
    
    /**
     * This method is used to register the root path of your site.
     *
     * @param string $_rootPath Your root path Ex: /_rootpath
     * @return _route
     */
    public function root($_rootPath) {
        //set current _rootPath
        $this->_rootPath = $_rootPath;
        //remove root from _currentRoute
        $this->_currentRoute = str_replace($this->_rootPath, '', $this->_currentRoute);
        return $this;
    }
    
    /**
     * This method is used to tell z20 which controllers to invoke for which
     * paths.
     *
     * @param string $path Path you'd like to match
     * @param string $controller Controller you'd like to invoke
     * @return z20router
     */
    public function when($path, $controller) {
        $this->_setRoute($path, $controller);
        return $this;
    }
    
    /**
     * This method is used to tell z20 which controller to invoke if it cannot
     * find a matching path.
     *
     * @param string $controller Controller you'd like to invoke
     * @return z20router
     */
    public function otherwise($controller) {
        $this->_setRoute('default', $controller);
        return $this;
    }
    
    /**
     * This method is used to force your application to redirect within your
     * application to the $path you give it.
     *
     * @param string $path Path to redirect to.
     */
    public function redirect($path) {
        if (substr($path, 0, 1) != '/') {
            $path = '/' . $path;
        }
        if (!strpos($path, $this->get_RootPath())) {
            $path = $this->get_RootPath() . $path;
        }
        header('location:' . $path);
    }
    
    /**
     * This method is used to resolve your route and determine which
     * controller to invoke.
     *
     * @return mixed Returns the controller to invoke if it finds a matched
     * route
     */
    public function resolveRoute() {
        $route_config = $this->_config->get('routes');
        $current_route = $this->getCurrentRoute();
        if (array_key_exists($current_route, $route_config)) {
            $this->_matchedRoute = $current_route;
            return $route_config[$current_route];
        } else {
            //remove url query params and parse route into its parts
            $route_q = explode('?', $current_route);
            $route_parts = explode('/', substr($route_q[0], 1));
            foreach ($route_config as $path => $controller) {
                if (strpos($path, ':') !== false) {
                    $route_match = true;
                    $path_parts = explode('/', substr($path, 1));
                    $i = 0;
                    foreach ($path_parts as $part) {
                        if ($route_match) {
                            $route_match = false;
                            if (isset($route_parts[$i]) && $route_parts[$i] != '') {
                                if (strpos($part, ':') !== false) {
                                    $route_match = true;
                                } elseif ($part == $route_parts[$i]) {
                                    $route_match = true;
                                }
                            }
                            $i++;
                        }
                    }
                    if (isset($route_parts[$i])) {
                        $route_match = false;
                    }
                    if ($route_match) {
                        $this->_matchedRoute = $path;
                        $matched_route = explode('/', substr($this->_matchedRoute, 1));
                        $i = 0;
                        foreach ($matched_route as $matched_route_part) {
                            if (strpos($matched_route_part, ':') !== false) {
                                $this->routeVars[substr($matched_route_part, 1)] = $route_parts[$i];
                            }
                            $i++;
                        }
                        return $controller;
                    }
                }
            }
            if (array_key_exists('default', $route_config)) {
                $this->_matchedRoute = false;
                return $route_config['default'];
            } else {
                return false;
            }
        }
    }
    
    /**
     * This method is used to get the values of the vars you set in your
     * matched route. Example: If you set a route to be /:id and your current
     * route was /5, than you would have a matched route and you could access
     * the value of :id using this method.
     *
     * @param string $routeParam the specific route parameter you would like to gain access to
     * @return array The key/value pairs of the vars that match
     */
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
    
    /**
     * This method is used to get the current route your application is in.
     *
     * @return string The current route
     */
    public function getCurrentRoute() {
        return $this->_currentRoute;
    }
    /**
     * This method is used to get the current path your application in is. The
     * difference between this method and self::get_CurrentRoute() is that the
     * this method returns your root path and with your current route.
     *
     * @return string The current path
     */
    
    public function getCurrentPath() {
        return $this->_rootPath . $this->_currentRoute;
    }
    /**
     * This method is used to get the current matched route.
     *
     * @return string Your matched route
     */
    
    public function get_MatchedRoute() {
        return $this->_matchedRoute;
    }
    /**
     * This method is used to get the root path you registered using
     * self::root().
     *
     * @return string The root path
     */
    public function get_RootPath() {
        return $this->_rootPath;
    }
    /**
     * This method is used to set routes in the configuration based on how
     * you are registering them.
     *
     * @param string $path
     * @param string $controller
     */
    private function _setRoute($path, $controller) {
        $routeConfig = $this->_config->get('routes');
        $routeConfig[$path] = $controller;
        $this->_config->set('routes', $routeConfig);
    }
}