<?php

namespace Core\Lib;

class Cache
{

	/**
	 * 架构函数
	 * @param array $options 缓存参数
	 * @access public
	 */
	function __construct($options = array())
	{
		if (!extension_loaded('memcache'))
			die('php extension memcache has‘nt  install.');


		$options = array_merge(array(
			'host' => config('MEMCACHE_HOST') ?: '127.0.0.1',
			'port' => config('MEMCACHE_PORT') ?: 11211,
			'timeout' => config('DATA_CACHE_TIMEOUT') ?: false,
			'persistent' => false,
			), $options);

		$this->options = $options;
		$this->options['expire'] = isset($options['expire']) ? $options['expire'] : config('DATA_CACHE_TIME');
		$this->options['prefix'] = isset($options['prefix']) ? $options['prefix'] : config('DATA_CACHE_PREFIX');
		$this->options['length'] = isset($options['length']) ? $options['length'] : 0;
		$this->handler = new \Memcache;
		if ($options['timeout'])
			$this->handler->connect($options['host'], $options['port'], $options['timeout']);
		else
			$this->handler->connect($options['host'], $options['port']);
	}

	/**
	 * 读取缓存
	 * @access public
	 * @param string $name 缓存变量名
	 * @return mixed
	 */
	public function get($name)
	{
		return $this->handler->get($this->options['prefix'] . $name);
	}

	/**
	 * 写入缓存
	 * @access public
	 * @param string $name 缓存变量名
	 * @param mixed $value  存储数据
	 * @param integer $expire  有效时间（秒）
	 * @return boolean
	 */
	public function set($name, $value, $expire = null)
	{
		if (is_null($expire)) {
			$expire = $this->options['expire'];
		}
		$name = $this->options['prefix'] . $name;
		if ($this->handler->set($name, $value, 0, $expire)) {
			if ($this->options['length'] > 0) {
				// 记录缓存队列
				$this->queue($name);
			}
			return true;
		}
		return false;
	}

	/**
	 * 删除缓存
	 * @access public
	 * @param string $name 缓存变量名
	 * @return boolean
	 */
	public function delete($name, $ttl = false)
	{
		$name = $this->options['prefix'] . $name;
		return $ttl === false ?
			$this->handler->delete($name) :
			$this->handler->delete($name, $ttl);
	}

	/**
	 * 清除缓存
	 * @access public
	 * @return boolean
	 */
	public function clear()
	{
		return $this->handler->flush();
	}

}
