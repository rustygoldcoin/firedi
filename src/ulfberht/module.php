<?php
/**
 * @package ulfberht
 * @author Joshua L. Johnson <josh@ua1.us>
 * @link http://ua1.us
 * @copyright Copyright 2016, Joshua L. Johnson
 * @license MIT
 */

namespace ulfberht;

use Exception;
use ulfberht\core\module as baseModule;
use ulfberht\module\router;
use ulfberht\module\request;
use ulfberht\module\response;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

class module extends baseModule {

    public function __construct() {
        $this->registerSingleton('ulfberht\module\config');
        $this->registerSingleton('ulfberht\module\doctrine');
        $this->registerSingleton('ulfberht\module\request');
        $this->registerSingleton('ulfberht\module\response');
        $this->registerSingleton('ulfberht\module\router');
        $this->registerSingleton('ulfberht\module\view');
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
        if (!ulfberht()->exists($controllerClass)) {
            throw new Exception('Could not find controller "' . $controllerClass . '"');
        }

        $controller = ulfberht()->get($controllerClass);
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