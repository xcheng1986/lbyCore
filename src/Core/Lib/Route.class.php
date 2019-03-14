<?php

namespace Core\Lib;

class Route
{

    /**
     * User Controller Auto Load
     */
    public static function controllerLoad($param = [])
    {
        global $argv;
        if (is_cli()) {
            $query = isset($argv[1]) ? ltrim($argv[1], '/') : '';
        } else {
            $query = ltrim($_SERVER['REQUEST_URI'], '/');
        }

        $place = strpos($query, '?');
        if ($place !== false) {
            $query = substr($query, 0, $place);
        }

        $query = str_replace('.html', '', $query) ?: '';
        $default_controller = config('DEFAULT_CONTROLLER');
        $default_method = config('DEFAULT_ACTION');

        if ($query == '' || $query == '/') {
            $class = $default_controller;
            $method = $default_method;
        } else {
            $query = ucwords(preg_replace('/\/+/', '/', $query), '/');
            $place2 = strripos($query, '/');
            if ($place2 === FALSE) {
                if (is_dir(APP_PATH . DIRECTORY_SEPARATOR . 'Controller' . DIRECTORY_SEPARATOR . ucfirst($query))) {
                    $query = ucfirst($query) . '/' . $default_controller . '/' . $default_method;
                } else {
                    $query = ucfirst($query) . '/' . $default_method;
                }
            }
            $place2 = strripos($query, '/');
            
            $class = substr($query, 0, $place2);
            $method = substr($query, $place2 + 1, strlen($query) - $place2);
        }

        $file = APP_PATH . DIRECTORY_SEPARATOR . 'Controller' . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $class) . '.class.php';
        if (!is_file($file)) {
            die('file "' . $file . '" not found');
        }

        $class_path = '\App\Controller\\' . str_replace('/', '\\', $class);
        try {
            $class = new $class_path;
            $class->$method($param);
        } catch (Exception $exc) {
            echo $exc->getTraceAsString();
        }
    }

}
