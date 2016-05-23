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
use ulfberht\module\request;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;

class router {

    private $_request;
    private $_routeCollection;
    private $_defaultController;

    public function __construct(request $request) {
        $this->_request = $request;
        $this->_routeCollection = new RouteCollection();
        $this->_defaultController = false;
    }

    public function when($path, $controller) {
        $this->_addRoute($path, $controller);
        return $this;
    }

    public function get($path, $controller) {
        $this->_addRoute($path, $controller, 'GET');
        return $this;
    }

    public function post($path, $controller) {
        $this->_addRoute($path, $controller, 'POST');
        return $this;
    }

    public function put($path, $controller) {
        $this->_addRoute($path, $controller, 'PUT');
        return $this;
    }

    public function delete($path, $controller) {
        $this->_addRoute($path, $controller, 'DELETE');
        return $this;
    }

    public function otherwise($controller) {
        $this->_defaultController = $controller;
        return $this;
    }

    public function resolve() {
        $currentRoute = $this->_request->server->get('REQUEST_URI');
        $context = new RequestContext();
        $context->fromRequest($this->_request);
        $matcher = new UrlMatcher($this->_routeCollection, $context);
        try {
            $parameters = $matcher->matchRequest($this->_request);
        } catch (Exception $e) {
            if ($this->_defaultController) {
                return [
                    'controller' => $this->_defaultController,
                    '_route' => 'default'
                ];
            }
            throw new Exception('Could not find route "' . $currentRoute . '"');
        }

        return $parameters;
    }

    private function _addRoute($path, $controller, $method = '') {
        $route = new Route($path, ['controller' => $controller]);
        if ($method) {
            $route->setMethods($method);
        }
        $this->_routeCollection->add($path, $route);
    }

}