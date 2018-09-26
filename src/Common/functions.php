<?php

/**
 * 打印数据
 * @param type $data
 */
function p($data)
{
	echo '<pre>' . print_r($data, true) . '</pre>';
}

/**
 *
 * @param type $actionPath
 * @param type $param
 */
function w($actionPath, $param = null)
{
	list($action, $method) = explode('/', $actionPath);
	$class = '\App\Widget\\' . $action;
	return (new $class)->$method($param);
}

/**
 * 获取配置
 * @param type $key
 */
function config($key)
{
	$core_conf = include CORE_PATH . '/Conf/config.php';
	$user_conf = [];
	if (file_exists(APP_PATH . '/Conf/config.php'))
		$user_conf = include APP_PATH . '/Conf/config.php';
	$config = array_merge($core_conf, $user_conf);
	return isset($config[$key]) ? $config[$key] : null;
}

/**
 * 数据库
 * @param type $table
 * @return \Core\Lib\Db
 */
function db()
{
	return \Core\Lib\Db::getInstance();
}

/**
 *
 * @param type $key
 * @param type $value
 */
function session($key, $value = null)
{

}

/**
 *
 * @param type $key
 * @param type $value
 */
function cache($key, $value = null)
{

}
