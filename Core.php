<?php

namespace Core;

class Core
{

	public static $classMap = [];

	public static function run()
	{
		//自动加载
		spl_autoload_register('self::autoLoad', true);

		//加载配置文件及公用函数库
		self::autoInclude();

		session_name("LBY_PHOTO");
		$hander = new \Core\Lib\Session();
		session_set_save_handler(
			array(&$hander, "open"), array(&$hander, "close"), array(&$hander, "read"), array(&$hander, "write"), array(&$hander, "destroy"), array(&$hander, "gc")
		);
		session_start();

		//REQUEST METHOD
		self::defineMethod();
		//路由控制
		\Core\Lib\Route::controllerLoad();
	}

	/**
	 * 加载配置文件及公用函数库
	 */
	public static function autoInclude()
	{
		//加载系统函数库
		include (CORE_PATH . '/Common/functions.php');
		if (is_file(APP_PATH . '/Common/functions.php')) {
			include APP_PATH . '/Common/functions.php';
		}
	}

	/**
	 * 自动加载
	 * @param type $class
	 */
	public static function autoLoad($class)
	{
		$classFIleName = str_replace('\\', '/', $class) . '.class.php';
		if (isset(self::$classMap[$classFIleName]) || isset(self::$classMap[$classFIleName]))
			return true;
		$place = strpos($classFIleName, '/');
		$name = strtolower(substr($classFIleName, 0, $place));
		$file = '';
		if ($name == 'core') {
			$file = CORE_PATH . '/' . substr($classFIleName, $place);
		} else if ($name == 'app') {
			$file = APP_PATH . '/' . substr($classFIleName, $place);
		}
		if (is_file($file)) {
			self::$classMap[$classFIleName] = $classFIleName;
			include $file;
			return true;
		}
	}

	/**
	 * defineMethod
	 */
	public static function defineMethod()
	{
		define('REQUEST_METHOD', $_SERVER['REQUEST_METHOD']);
		define('IS_GET', REQUEST_METHOD == 'GET' ? true : false);
		define('IS_POST', REQUEST_METHOD == 'POST' ? true : false);
		define('IS_PUT', REQUEST_METHOD == 'PUT' ? true : false);
		define('IS_DELETE', REQUEST_METHOD == 'DELETE' ? true : false);
		define('IS_AJAX', (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' ) ? true : false );
	}

}
