<?php

/**
 * index.php 入口文件
 */
date_default_timezone_set("Asia/Shanghai");
header('Content-Type: text/html; charset=utf-8');
error_reporting(0);
//ini_set('display_errors','On');
//define('SYS_DEBUG',true);
/**
 * 定义项目所在路径(APP_ROOT)
 */
define('IN_FINECMS', true);
define('APP_ROOT',   dirname(__FILE__) . DIRECTORY_SEPARATOR);
$config = require APP_ROOT . 'config/config.ini.php';
require APP_ROOT . './core/App.php';
require APP_ROOT . './core/Config.php';
define('API_PATH',APP_ROOT."./controllers/api");
define('TRY_PATH',APP_ROOT."./controllers/try");
define('MOB_PATH',APP_ROOT."./controllers/mobi");
define('NEW_MOB_PATH',APP_ROOT."./controllers/mobi2");
$s = $_REQUEST['s'];
$c= $_REQUEST['c'];
//var_dump($_SERVER);exit;
if($s){
    if(($s=='index'&& $c=='api') ||  ($s=='admin'&& $c=='content')){        
    }else if( !in_array($s, array('mobi','mobi2'))){     
            header("HTTP/1.0 404 Not Found");
            exit;
    }
}
/**
* 启动网站进程
 */
App::run($config);