<?php

namespace Core\Lib;

class Controller
{

	public $assign = [];

	public function __contruct()
	{

	}

	public function setResult($code, $message = '', $data = null, $ext = null)
	{
		header('Content-type: application/json;charset=utf-8');
		$arr = ['status' => $code, 'info' => $message, 'data' => $data];
		if (!is_null($ext)) {
            $arr['ext'] = $ext;
        }
        die(json_encode($arr));
    }

	/**
	 * 显示模板
	 * @param string $template
	 */
	public function display($template = null)
	{
        $template = APP_PATH . '/View/' . $template . '.html';
		extract($this->assign);
		if (!is_file($template)) {
			echo $template;
			$this->error('页面错误');
		}
		include $template;
	}

	/**
	 * 模板赋值
	 * @param type $name
	 * @param type $value
	 * @return $this
	 */
	public function assign($name, $value)
	{
		$this->assign[$name] = $value;
		return $this;
	}

	/**
	 * 错误页面
	 * @param type $content
	 * @param type $jump_url
	 */
	public function error($content = '', $jump_url = '')
	{
		$template = config('ERROR_JUMP_TEMPLATE');
		if (is_file($template)) {
			$this->assign('content', $content)->assign('jump_url', $jump_url);
			include $template;
		}
		exit(1);
	}

	/**
	 * 成功页面
	 * @param type $content
	 * @param type $jump_url
	 */
	public function success($content = '', $jump_url = '')
	{
		$template = config('SUCCESS_JUMP_TEMPLATE');
		if (is_file($template)) {
			$this->assign('content', $content)->assign('jump_url', $jump_url);
			include $template;
		}
		exit(0);
	}

}
