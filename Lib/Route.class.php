<?php

namespace Core\Lib;

class Route
{

	/**
	 * User Controller Auto Load
	 */
	public static function controllerLoad()
	{
		$query = ltrim($_SERVER['REQUEST_URI'], '/');
		$place = strpos($query, '?');
		if ($place !== false)
			$query = substr($query, 0, $place);

		$query = str_replace('.html', '', strtolower($query)) ?: '';
		$default_controller = config('DEFAULT_CONTROLLER');
		$default_method = config('DEFAULT_ACTION');

		if ($query == '') {
			$class = $default_controller;
			$method = $default_method;
		} else {
			$query_real = preg_replace('/\/+/', '/', $query);
			$query_arr = explode('/', $query_real);
			if ($query_arr[0] == 'admin') {
				$class = 'Admin/' . ucfirst(isset($query_arr[1]) ? $query_arr[1] : $default_controller);
				$method = isset($query_arr[2]) ? $query_arr[2] : $default_method;
			} else {
				$class = ucfirst($query_arr[0]);
				$method = isset($query_arr[1]) ? $query_arr[1] : $default_method;
			}
		}
		$file = APP_PATH . '/Controller/' . $class . '.class.php';
		if (!is_file($file))
			die('file "' . $file . '" not found');

		$class_path = '\App\Controller\\' . str_replace('/', '\\', $class);
		$class = new $class_path;
		$class->$method();
	}

}
