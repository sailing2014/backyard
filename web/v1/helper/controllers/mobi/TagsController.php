<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 7/31 0031
 * Time: 14:54
 */
    include_once CONTROLLER_DIR.'admin/Common.php';
class TagsController extends Common{

    public function __construct() {
        parent::__construct();

        define('MOBI_PATH','views/mobi');
        $this->httpclient	= $this->instance('http_client');
        $api_list 			= \Config::get('api_list');
        static::$api_list 	= $api_list;
        //dump($api_list['api']);
        $time = time();
        $this->param = array(
            'api_key'   => $api_list['api']['api_key'],
            'api_token' => sha1($api_list['api']['api_secret'] . $time),
            'time'      => $time
        );
        $this->view->setTheme('');
        //dump($this->param);
    }

    public function indexAction(){

    }

    public function listAction(){

    }
}