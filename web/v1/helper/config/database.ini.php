<?php
if (!defined('IN_FINECMS')) exit();
return \Config::get('database');
/**
 * 数据库配置信息
 */
//======= local start =========
//return array(
//
//	'host'     => 'localhost', 
//	'username' => 'root', 
//	'password' => '', 
//	'dbname'   => 'helper', 
//	'prefix'   => 'fn_', 
//	'charset'  => 'utf8', 
//	'port'     => '', 
//
//);

//======= local end =========
//====== developing  start ========
//return array(
//
//	'host'     => '10.171.248.28', 
//	'username' => '50161-all-rw', 
//	'password' => 'Yaa$#@@sadf&', 
//	'dbname'   => 'helper', 
//	'prefix'   => 'fn_', 
//	'charset'  => 'utf8', 
//	'port'     => '', 
//
//);
//====== developing end  ========
//====== testing start ========
//return array(
//
//	'host'     => '10.168.2.126', 
//	'username' => '50161-all-rw', 
//	'password' => 'helper#%@&*%', 
//	'dbname'   => 'helper', 
//	'prefix'   => 'fn_', 
//	'charset'  => 'utf8', 
//	'port'     => '50161', 
//
//);
//====== testing end ========