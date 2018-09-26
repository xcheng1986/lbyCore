<?php

return array(
	'DEFAULT_CONTROLLER' => 'Index', // 默认控制器名称
	'DEFAULT_ACTION' => 'index', // 默认操作名称
	#mysql
	'MYSQL_OPTION' => array(
		'host' => '127.0.0.1',
		'user' => 'user',
		'password' => '',
		'database' => 'test',
		'port' => 3306,
		'socket' => '',
	),
	'LOG_FILE_DIR' => APP_PATH . '/Runtime',
	#跳转页面
	'ERROR_JUMP_TEMPLATE' => CORE_PATH . '/View/error_jump.html',
	'SUCCESS_JUMP_TEMPLATE' => CORE_PATH . '/View/success_jump.html',
	#
	'SQL_LOG' => false, //sql日志
);
