<?php

namespace Core\Lib;

/**
 * Description of Session
 *
 * @author Administrator
 */
class Session
{

	/**
	 * Session有效时间
	 */
	protected $lifeTime = '';

	/**
	 * session保存的数据库名
	 */
	protected $sessionTable = '';

	/**
	 * 数据库句柄
	 */
	protected $hander = array();

	/**
	 * 打开Session
	 * @access public
	 * @param string $savePath
	 * @param mixed $sessName
	 */
	public function open($savePath, $sessName)
	{
		$this->lifeTime = \config('SESSION_EXPIRE') ? \config('SESSION_EXPIRE') : ini_get('session.gc_maxlifetime');
		$this->sessionTable = \config('SESSION_TABLE') ?: 'session';
		$mysql_conf = config('MYSQL_OPTION');
		//从数据库链接
		$hander = @mysql_connect($mysql_conf['host'] . ':' . $mysql_conf['port'], $mysql_conf['user'], $mysql_conf['password']);
		$dbSel = mysql_select_db($mysql_conf['database'], $hander);
		if (!$hander || !$dbSel)
			return false;
		$this->hander = $hander;
		return true;
	}

	/**
	 * 关闭Session
	 * @access public
	 */
	public function close()
	{
		if (is_array($this->hander)) {
			$this->gc($this->lifeTime);
			return (mysql_close($this->hander));
		}
		$this->gc($this->lifeTime);
		return mysql_close($this->hander);
	}

	/**
	 * 读取Session
	 * @access public
	 * @param string $sessID
	 */
	public function read($sessID)
	{
		$hander = $this->hander;
		$res = mysql_query('SELECT session_data AS data FROM ' . $this->sessionTable . " WHERE session_id = '$sessID'   AND session_expire >" . time(), $hander);
		if ($res) {
			$row = mysql_fetch_assoc($res);
			$expire = time() + $this->lifeTime;
			//更新最后的读的时间
			mysql_query('update ' . $this->sessionTable . " set session_expire= $expire WHERE session_id = '$sessID' limit 1", $hander);
			return $row['data'];
		}
		return "";
	}

	/**
	 * 写入Session
	 * @access public
	 * @param string $sessID
	 * @param String $sessData
	 */
	public function write($sessID, $sessData)
	{
		$hander = $this->hander;
		$expire = time() + $this->lifeTime;
		mysql_query('REPLACE INTO  ' . $this->sessionTable . " (  session_id, session_expire, session_data)  VALUES( '$sessID', '$expire',  '" . addslashes($sessData) . "')", $hander);
		if (mysql_affected_rows($hander))
			return true;
		return false;
	}

	/**
	 * 删除Session
	 * @access public
	 * @param string $sessID
	 */
	public function destroy($sessID)
	{
		$hander = $this->hander;
		mysql_query('DELETE FROM ' . $this->sessionTable . " WHERE session_id = '$sessID' limit 1", $hander);
		if (mysql_affected_rows($hander))
			return true;
		return false;
	}

	/**
	 * Session 垃圾回收
	 * @access public
	 * @param string $sessMaxLifeTime
	 */
	public function gc($sessMaxLifeTime)
	{
		$hander = $this->hander;
		mysql_query('DELETE FROM ' . $this->sessionTable . ' WHERE session_expire < ' . time(), $hander);
		return mysql_affected_rows($hander);
	}

}
