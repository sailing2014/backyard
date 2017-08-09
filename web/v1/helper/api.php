<?php

/**
 * index.php 入口文件
 */
header('Content-Type:application/json; charset=utf-8');

/**
 * 定义项目所在路径(APP_ROOT)
 */
define('IN_FINECMS', true);
define('APP_ROOT',   dirname(__FILE__) . DIRECTORY_SEPARATOR);
$config = require APP_ROOT . 'config/config.ini.php';
require APP_ROOT . './core/App.php';
require APP_ROOT . './core/Config.php';
$s = $_REQUEST['s'];
if($s == "admin"){
    header("HTTP/1.0 404 Not Found");
    exit;
}
/**
* 启动网站进程
 */
App::run($config);