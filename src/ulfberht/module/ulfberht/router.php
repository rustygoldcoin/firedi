<?php

namespace ulfberht\module\ulfberht;

use Exception;
use ulfberht\module\ulfberht\request;
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
        $route = new Route($path, ['controller' => $controller]);
        $this->_routeCollection->add($path, $route);
        return $this;
    }
    
    public function otherwise($controller) {
        $this->_defaultController = $controller;
        return $this;
    }
    
    public function resolve() {
        $currentRoute = $this->_request->server->get('REQUEST_URI');
        $context = new RequestContext('/');
        $matcher = new UrlMatcher($this->_routeCollection, $context);
        try {
            $parameters = $matcher->match($currentRoute);
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
    
}