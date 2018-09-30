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

    public function __construct()
    {
        $this->lifeTime = \config('SESSION_EXPIRE') ? \config('SESSION_EXPIRE') : ini_get('session.gc_maxlifetime');
        $this->sessionTable = \config('SESSION_TABLE') ?: 'session';
        $this->hander = db();
    }

    /**
     * 打开Session
     * @access public
     * @param string $savePath
     * @param mixed $sessName
     */
    public function open($savePath, $sessName)
    {
        return true;
    }

    /**
     * 关闭Session
     * @access public
     */
    public function close()
    {
        $this->gc($this->lifeTime);
        return true;
    }

    /**
     * 读取Session
     * @access public
     * @param string $sessID
     */
    public function read($sessID)
    {
        $hander = $this->hander;
        $res = $hander->find('SELECT session_data AS data FROM ' . $this->sessionTable . " WHERE session_id = '$sessID'   AND session_expire >" . time());
        if ($res) {
            $expire = time() + $this->lifeTime;
            //更新最后的读的时间
            $hander->update('update ' . $this->sessionTable . " set session_expire= $expire WHERE session_id = '$sessID' limit 1");
            return $res['data'];
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
        $expire = time() + $this->lifeTime;
        $this->hander->update("REPLACE INTO  " . $this->sessionTable . " (  session_id, session_expire, session_data)  VALUES( '$sessID', '$expire',  '" . addslashes($sessData) . "')");
        return true;
    }

    /**
     * 删除Session
     * @access public
     * @param string $sessID
     */
    public function destroy($sessID)
    {
        $this->hander->update('DELETE FROM ' . $this->sessionTable . " WHERE session_id = '$sessID' limit 1");
        return true;
    }

    /**
     * Session 垃圾回收
     * @access public
     * @param string $sessMaxLifeTime
     */
    public function gc($sessMaxLifeTime)
    {
        $this->hander->update('DELETE FROM ' . $this->sessionTable . ' WHERE session_expire < ' . time());
        return true;
    }

}
