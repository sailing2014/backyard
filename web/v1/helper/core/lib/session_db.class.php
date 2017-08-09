<?php
/**
 * session class file
 * 处理session操作
 */

if (!defined('IN_FINECMS')) exit();

class session_db extends Fn_base {

	/**
     * Session有效时间
     */
   protected $lifeTime      = ''; 

    /**
     * session保存的数据库名
     */
   protected $sessionTable  = '';

    /**
     * 数据库句柄
     */
   protected $hander  = array(); 

   /**
    * 数据库配置
    */
   protected $db = array();

   public function __construct(){
   		//@register_shutdown_function(array($this, 'close'));
        error_reporting(0);

   		$this->db = \Config::get('database');
   		//$this->open();
   		$this->Session();
   		
   	}
   	/**
   	 * 
   	 */
	function Session(){ 
		session_set_save_handler( 
			array(&$this, "open"), 
			array(&$this, "close"), 
			array(&$this, "get"), 
			array(&$this, "set"), 
			array(&$this, "delete"), 
			array(&$this, "gc")
		);
		session_start();
	}

    /**
     * 打开Session 
     * @access public 
     * @param string $savePath 
     * @param mixed $sessName  
     */
    public function open($savePath='', $sessName='') { 
		$session = $this->db['session'];
		$this->lifeTime = $session['expire'] ? $session['expire'] : ini_get('session.gc_maxlifetime');
		$this->sessionTable  =   $session['prefix'].($session['table'] ? $session['table'] : "session");
		//分布式数据库
		
       //##主数据库链接##
	   $master = $this->db['mysql_master'];
	   $w = floor(mt_rand(0,count($master)-1));
       $hander = mysql_connect($master[$w]['host'].':'.$master[$w]['port'], $master[$w]['username'], $master[$w]['password']) or die ( 'Not connected : '  .  mysql_error ());
       $dbSel = mysql_select_db($master[$w]['dbname'], $hander);
       if(!$hander || !$dbSel)
           return false;
       $this->hander[0] = $hander;

       //##从数据库链接##
       $slave  = $this->db['mysql_slave'];
       $r = floor(mt_rand(0,count($slave)-1));
       $hander = mysql_connect($slave[$r]['host'].':'.$slave[$r]['port'], $slave[$r]['username'], $slave[$r]['password']) or die ( 'Not connected : '  .  mysql_error ());
       $dbSel = mysql_select_db($slave[$r]['dbname'], $hander);
       if(!$hander || !$dbSel)
           return false;
       $this->hander[1] = $hander;

       // $result = mysql_query('select * from fn_sessions',$this->hander[0]);
       // var_dump(mysql_fetch_array($result)); echo "<br/>";
       //var_dump($this->hander); echo "<br/>";
       return true;
    } 

    /**
     * 关闭Session 
     * @access public 
     */
   public function close() {
       if(is_array($this->hander)){
           $this->gc($this->lifeTime);
           return (mysql_close($this->hander[0]) && mysql_close($this->hander[1]));
       }
       $this->gc($this->lifeTime); 
       return mysql_close($this->hander); 
   } 

    /**
     * 读取Session 
     * @access public 
     * @param string $sessID 
     */
   public function get($sessID) { 
   	//echo "get<br/>";
   	//var_dump($this->hander); echo "<br/>";
       $hander = is_array($this->hander) ? $this->hander[1] : $this->hander;//从
       $res = mysql_query("SELECT session_data AS data FROM " . $this->sessionTable . " WHERE session_id = '$sessID' AND session_expire >".time(), $hander); 
       if($res) {
           $row = mysql_fetch_assoc($res);
           return $row['data']; 
       }
       return ""; 
   } 

    /**
     * 写入Session 
     * @access public 
     * @param string $sessID 
     * @param string $sessData  
     */
   public function set($sessID, $sessData) { 
   	//echo "set<br/>";
   	//var_dump($this->hander); echo "<br/>";
       $hander = is_array($this->hander) ? $this->hander[0] : $this->hander;//主
       $expire = time() + $this->lifeTime; 
       mysql_query("REPLACE INTO " .$this->sessionTable. "(session_id, session_expire, session_data)  VALUES( '$sessID', '$expire', '$sessData')", $hander);
//       error_log("REPLACE INTO " .$this->sessionTable. "(session_id, session_expire, session_data)  VALUES( '$sessID', '$expire', '$sessData')\n",3,"/home/bbc_helper/log/session.log");
       // $res = mysql_query('select * from fn_sessions', $hander);
       // var_dump(mysql_fetch_assoc($res));

       if(mysql_affected_rows($hander)) 
           return true; 
       return false; 
   } 

    /**
     * 删除Session 
     * @access public 
     * @param string $sessID 
     */
   public function delete($sessID) { 
   	//echo "delete<br/>";
   	//var_dump($this->hander); echo "<br/>";
       $hander = is_array($this->hander)?$this->hander[0]:$this->hander;
       mysql_query("DELETE FROM ".$this->sessionTable." WHERE session_id = '$sessID'",$hander); 
       if(mysql_affected_rows($hander)) 
           return true; 
       return false; 
   } 

   /**
    * 是否写入session
    * @access public 
    * @param string $sessID 
    */
   public function is_set($sessID){
   	//var_dump($this->hander); echo "<br/>";
   		$hander = is_array($this->hander) ? $this->hander[1] : $this->hander;//从
       	$res = mysql_query("SELECT count(*) AS count FROM " . $this->sessionTable . " WHERE session_id = '$sessID' AND session_expire >".time(), $hander); 
       	if($res = mysql_fetch_assoc($res) and $res['count']>0) {
           return true; 
       }
       return false; 
   }

   /**
    * ?todo
    */
   public function get_id(){
   		return session_id();
   }

    /**
     * Session 垃圾回收
     * @access public 
     * @param string $sessMaxLifeTime 
     */
   public function gc($sessMaxLifeTime) { 
       $hander = is_array($this->hander)?$this->hander[0]:$this->hander;
       mysql_query("DELETE FROM ".$this->sessionTable." WHERE session_expire < ".time(),$hander); 
       return mysql_affected_rows($hander); 
   } 


   /**
    * 
    */

}