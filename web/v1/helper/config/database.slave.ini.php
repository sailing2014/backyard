<?php
if (!defined('IN_FINECMS')) exit();
/**
  * 数据库读写分离配置
  * 支持多个从库，格式如：1=>array(), 2=>array() ... 
  * 从库的数据库名、前缀、编码必须与主库保持一致
 */
 
$db = \Config::get('database');
return $db['mysql_slave'];