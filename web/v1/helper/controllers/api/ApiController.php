<?php
class ApiController extends Common
{
    protected $api_key;
    protected $api_secret;

    public function __construct() {
        parent::__construct();
        $this->api_key = '32C73BA3F00E407A';
        $this->api_secret = '2683a517847ee76607a10677e092f6be';
        //$this->validate_token();
    }

    /**
     *
     */
    public function validate_token(){
        $api_key = $this->raw('api_key');
        $api_token = $this->raw('api_token');
        $time = $this->raw('time');
        $secret = $this->api_secret;
        //echo sha1($secret . $time);
        $response = array();
        if ($api_key != $this->api_key || $api_token != sha1($secret . $time)) {
            $response['status']['code'] = 1435;
            $response['status']['message'] = 'Unauthorized.';
            die(json_encode($response));
        }
    }

    /**
     * @param $param
     * @return mixed
     */
    public function raw($param){
        //$post = $GLOBALS['HTTP_RAW_POST_DATA'];//raw
        $post = file_get_contents("php://input");//raw
        $post = json_decode($post,true);
        return $post[$param];
    }

    /**
     *
     */
    public function ajaxReturn($return){
        header('Content-Type:application/json; charset=utf-8');
        exit(json_encode($return));
    }
}