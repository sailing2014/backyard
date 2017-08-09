<?php
include_once CONTROLLER_DIR . 'admin/Common.php';

class Cloud extends Admin
{

    protected $httpclient;
    protected $param;
    protected static $api_list;

    public function __construct()
    {
        parent::__construct();

        $this->httpclient = $this->instance('http_client');
        $api_list = \Config::get('api_list');
        static::$api_list = $api_list;
        //dump($api_list['api']);
        $time = time();
        $this->param = array(
            'api_key' => $api_list['api']['api_key'],
            'api_token' => sha1($api_list['api']['api_secret'] . $time),
            'time' => $time,
            'user_agent' => 'bbc_helper'
        );
        //dump($this->param);
    }

    public function index()
    {
        $this->view->display('admin/cloud/index.html');
    }

    /**
     * 
     */
    protected function item($type, $item = "")
    {
        $api_list = static::$api_list;
        if (empty($item)) {
            return $api_list[$type];
        }
        return $api_list[$type][$item];
    }

    /**
     *
     */
    public function ajaxReturn($return)
    {
        header('Content-Type:application/json; charset=utf-8');
        exit(json_encode($return));
    }
}
