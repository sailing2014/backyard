<?php
/**
 * 管理配置文件
 * @author root
 *
 */
class Config{
	
	public static $_config = array();
	
	/**
	 * 获取配置文件信息
	 */
	public static function get($item){
		$configData = array();
		if(!isset(static::$_config[$item]))	{
			// path = WEB_ROOT.'/../../../conf/'.$item.'.inc.php';
            $path = '/home/bbc_helper/conf/'.$item.'.inc.php';			
			if(file_exists($path)){
				$configData = require $path;
			}
			static::$_config[$item] = $configData;
		} else {
			$configData = static::$_config[$item];
		}
		return  $configData;
	}
	
}