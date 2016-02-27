<?php

/**
 * @package ulfberht
 * @author Joshua L. Johnson <josh@ua1.us>
 * @link http://labs.ua1.us
 * @copyright Copyright 2016, Joshua L. Johnson
 * @license MIT
 */

namespace ulfberht\module;

use Exception;
use ulfberht\core\module;
use ulfberht\module\ulfberht\router;
use ulfberht\module\ulfberht\request;
use ulfberht\module\ulfberht\response;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

class ulfberht extends module {

    public function __construct() {
        //register config service
        $this->registerSingleton('ulfberht\module\ulfberht\config');
        $this->registerSingleton('ulfberht\module\ulfberht\doctrine');
        $this->registerSingleton('ulfberht\module\ulfberht\router');
        $this->registerSingleton('ulfberht\module\ulfberht\request');
        $this->registerSingleton('ulfberht\module\ulfberht\response');
        $this->registerSingleton('ulfberht\module\ulfberht\view');
    }

    public function mvc(router $router, request $request, response $response) {
        $params = $router->resolve();
        $request->attributes->add($params);
        $controllerActionSetting = explode(':', $params['controller']);
        $controllerClass = $controllerActionSetting[0];
        $controllerAction = (isset($controllerActionSetting[1])) ? $controllerActionSetting[1] : false;
        if (!$controllerClass) {
            throw new Exception('Could not find a controller to resolve.');
        }
        if (!$controllerAction) {
            throw new Exception('A controller action was not defined.');
        }
        if (!ulfberht()->isService($controllerClass)) {
            throw new Exception('Could not find controller "' . $controllerClass . '"');
        }

        $controller = ulfberht()->getService($controllerClass);
        if ($controllerAction) {
            if (!method_exists($controller, $controllerAction)) {
                throw new Exception('Cound not find action method "' . $controllerAction . '" on controller "' . $controllerClass . '"');
            }
            call_user_func([$controller, $controllerAction]);
        }
        
        $response->prepare($request);
        $response->send();
    }

}