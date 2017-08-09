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
define('APP_ROOT',   dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR);
$config = require APP_ROOT . 'config/config.ini.php';
require APP_ROOT . './core/App.php';
require APP_ROOT . './core/Config.php';
define('API_PATH',APP_ROOT."./controllers/api");
define('TRY_PATH',APP_ROOT."./controllers/try");
define('MOB_PATH',APP_ROOT."./controllers/mobi");
$s = $_REQUEST['s'];
if($s != "admin"){
    header("HTTP/1.0 404 Not Found");
    exit;
}
$HTTP_SSL_PROTOCOL = isset($_SERVER['HTTP_SSL_PROTOCOL']) ? $_SERVER['HTTP_SSL_PROTOCOL'] : "";
$is_https = false;
if(strpos($HTTP_SSL_PROTOCOL,"TLS") === 0 || strpos($HTTP_SSL_PROTOCOL,"SSL") === 0){
    $is_https = true;
}
if(!$is_https)
{
//     header("HTTP/1.0 403 Forbidden");
//     echo "403 Forbidden";
//     exit;
}


/**
* 启动网站进程
 */
App::run($config);