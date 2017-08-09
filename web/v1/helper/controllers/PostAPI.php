<?php

class PostAPI extends Common
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

        $time = time();

        $this->param = array(
            'api_key' => $api_list['api']['api_key'],
            'api_token' => sha1($api_list['api']['api_secret'] . $time),
            'time' => $time,
            'user_agent' => 'bbc_helper'
        );
    }

    /**
     * 
     */
    protected function item($type, $item)
    {

        $api_list = static::$api_list;

        return $api_list[$type][$item];
    }
}
