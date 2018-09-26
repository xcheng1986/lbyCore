<?php

namespace Core\Lib;

class Log
{

	public static function write($info, $level = 'INFO')
	{
		$file_path = config('LOG_FILE_DIR');
		if (!is_dir($file_path)) {
			if (@mkdir($file_path, 0777, true) == false)
				die('create Runtime dir error');
		}
		$filename = $file_path . '/' . date('Y-m-d') . '.log';
		file_put_contents($filename, "[" . date('Y-m-d H:i:s') . "][" . $level . "] $info\r\n", FILE_APPEND);
	}

}
