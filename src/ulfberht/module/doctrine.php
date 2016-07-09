<?php

namespace module;

use ulfberht\core\module;
use ulfberht\module\config;
use ulfberht\module\request;
use ulfberht\module\response;
use ulfberht\module\router;

/**
 * The application module is the backbone of Ulfberht Applications and provides much of the
 * automatical things we see like initializing configurations.
 */

class application extends module {

    /**
     * The Constructor
     */
    public function __construct() {
        $this->dependsOn('ulfberht\module');
        $this->dependsOn('module\debugger');
    }

    /**
     * Initializes the ulfberht application
     *
     * @param $config ulfberht\module\config
     * @param $config ulfberht\module\request
     * @param $config ulfberht\module\router
     */
    public function applicationInit(config $config, request $request, router $router) {
        $this->applicationInitConfig($config, $request);
        $this->applicationInitRoutes($config, $router);
    }

    /*
     * This method is responsible for loading in initial application configurations from
     * application ini files located in /src/module/application/configs/application.ini
     *
     * @param $config ulfberht\module\config
     * @param $request ulfberht\module\request
     */
    public function applicationInitConfig(config $config, request $request) {
        //get app config and loop though additional configs
        $appConfigSrcPath = APPLICATION_ROOT . '/src/module/application/config/application.ini';
        $appConfigIni = $this->_getConfigIniFromIniPath($appConfigSrcPath);
        $appConfigName = $this->_getFileNameFromIniPath($appConfigSrcPath);
        $config->set($appConfigName, $appConfigIni);

        //look through application config configs array and setup those configs
        $configs = ($appConfigIni->configs) ? $appConfigIni->configs : [];
        foreach ($appConfigIni->configs as $configPath) {
            $configSrcPath = APPLICATION_ROOT . $configPath;
            $configIni = $this->_getConfigIniFromIniPath($configSrcPath);
            $configName = $this->_getFileNameFromIniPath($configSrcPath);
            $config->set($configName, $configIni);
        }

        //setup env config
        $host = $request->server->get('HTTP_HOST');

        //if host comes back undefined assume localhost:8000 settings
        if (!$host) {
            $host = 'localhost:8000';
        }
        $envConfig = $config->get('environment')->{$host};
        $config->set('ENV', $envConfig);
    }

    /**
     * This method is responsible for loading in routes based on routes.ini
     *
     * @param $config ulfberht\module\config
     * @param $request ulfberht\module\router
     */
    public function applicationInitRoutes(config $config, router $router) {
        $routesConfig = $config->get('routes');
        foreach (['get', 'put', 'post', 'delete'] as $verb) {
            $routes = $routesConfig->{$verb}->routes;
            if (isset($routes) && is_object($routes)) {
                foreach ($routes as $path => $controller) {
                    $router->{$verb}($path, $controller);
                }
            }
        }
        //setting default controller if it is set in routes.ini
        if ($routesConfig->default) {
            $router->otherwise($routesConfig->default);
        }
    }

    /**
     * This method is responsible for returning a configIni object wrapping
     * which ever ini file is passed in.
     *
     * @param $fileSrcPath string - The path to the ini file.
     * @return string
     */
    private function _getConfigIniFromIniPath($fileSrcPath) {
        $parser = new \IniParser($fileSrcPath);
        return $parser->parse();
    }

    /**
     * Returns the filename of the files source path you pass into it.
     *
     * @param $fileSrcPath string - The path to the ini file.
     * @return string The filename parsed out of the path.
     */
    private function _getFileNameFromIniPath($fileSrcPath) {
        $pathParts = preg_split('/\//', $fileSrcPath);
        $fileName = $pathParts[count($pathParts) - 1];
        $fileNameParts = preg_split('/\./', $fileName);

        return $fileNameParts[0];
    }

}
