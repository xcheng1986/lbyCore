<?php

namespace Core\Lib;

class Db
{

	public static $_instance;
	private $handel;

	private function __construct()
	{
		$c = config('MYSQL_OPTION');
		$this->handel = mysqli_connect($c['host'], $c['user'], $c['password'], $c['database'], $c['port'], $c['socket']);
		if (mysqli_connect_errno($this->handel))
			die("连接 MySQL 失败: " . mysqli_connect_error());
		$this->handel->query("SET NAMES utf8");
	}

	/**
	 * 单例方法,用于访问实例的公共的静态方法
	 * @return type
	 */
	public static function getInstance()
	{
		if (!(self::$_instance instanceof self)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * query
	 * @param type $sql
	 * @return type
	 */
	public function select($sql)
	{

		$R = [];
		if (config('SQL_LOG'))
			Log::write($sql, 'SQL_LOG');

		if ($sql == '')
			return false;

		$result = $this->handel->query($sql);
		if (!$result) {
			echo $sql . '<br/>';
			return [];
		}
		while ($row = mysqli_fetch_assoc($result))
			$R[] = $row;
		return $R;
	}

	/**
	 * find
	 * @param type $sql
	 * @return type
	 */
	public function find($sql)
	{
		$R = $this->select($sql);
		if (!empty($R))
			return $R[0];
		else
			return [];
	}

	/**
	 * count
	 * @param type $sql "... count(*) as _count_ ..."
	 * @return type
	 */
	public function count($sql)
	{
		$data = $this->find($sql);
		if (!isset($data['_count_']))
			die('_count_ is not define');
		return (int) ($data['_count_'] ?: 0);
	}

	/**
	 * 更新操作
	 * @return intval 受影响的行数
	 */
	public function update($sql)
	{
		if (config('SQL_LOG'))
			Log::write($sql, 'SQL_LOG');
		$result = $this->handel->query($sql);
		if ($result === false)
			return false;
		return $this->handel->affected_rows;
	}

	/**
	 * 插入一条数据
	 * @param type $sql
	 * @return 最后插入的id
	 */
	public function insert_one($sql, $return_insertId = true)
	{
		$res = $this->update($sql);
		if (!$res)
			return false;
		if ($return_insertId)
			return $this->handel->insert_id;
		else
			return TRUE;
	}

	/**
	 * 取得数据表的字段信息
	 */
	public function getFields($tableName)
	{
		$sql = 'show columns from ' . $tableName;
		return $this->select($sql);
	}

	/**
	 * 启动事务
	 */
	public function startTrans()
	{
		$this->handel->autocommit(false);
		return TRUE;
	}

	/**
	 * 用于非自动提交状态下面的查询提交
	 */
	public function commit()
	{
		$this->handel->commit();
		return TRUE;
	}

	/**
	 * 事务回滚
	 */
	public function rollback()
	{
		$this->handel->rollback();
		return TRUE;
	}

}
