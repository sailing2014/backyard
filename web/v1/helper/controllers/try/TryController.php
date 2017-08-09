<?php
include_once CONTROLLER_DIR.'admin/Common.php';
class TryController extends Admin{
    protected $httpclient;
    protected $param;
    protected static $api_list;
    public function __construct() {
        parent::__construct();

        $this->user = $this->model('user');
        $this->isAdminLogin('try');
        if (!auth::check($this->roleid, $this->controller . '-' . $this->action, $this->namespace)) {
            $this->adminMsg(lang('a-com-0', array('1' => $this->controller, '2' => $this->action)));
        }
        $sites	= App::get_site();
        $this->site_url	= 'http://' . $sites[$this->siteid]['DOMAIN'];
        $this->view->assign(array(
            'userinfo'	=> $this->userinfo,
            'site_url'	=> $this->site_url
        ));
        $this->adminLog();
        //****

        $this->httpclient	= $this->instance('http_client');
        $api_list 			= \Config::get('api_list');
        static::$api_list 	= $api_list;
        //dump($api_list['api']);
        $time = time();
        $this->param = array(
            'verify'    => array(
                'method' => 2,
                'api_key'   => $api_list['api']['api_key'],
                'api_token' => sha1($api_list['api']['api_secret'] . $time),
                'time'      => $time,
            ) //
        );
        $this->view->setTheme('');
//        dump($this->param);
    }
    /**
     *
     */
    protected function item($type,$item=""){
        $api_list = static::$api_list;
        if(empty($item)){
            return $api_list[$type];
        }
        return $api_list[$type][$item];
    }

    /**
     *
     */
    public function ajaxReturn($return){
        header('Content-Type:application/json; charset=utf-8');
        exit(json_encode($return));
    }

    /**
     * 获取任意数据
     */
    public function getAny($ids,$field,$model,$action){
        $url = $this->item('tryout');
        $param['model'] = $model;
        $param['action'] = $action;
        $param['doc_id'] = $ids;
        $result = $this->httpclient->post($url,$param);
        if($result['_status']['_code']==200){
            foreach($result['_data'] as $k=>$v){

            }
        }

    }

    /**
     * 获取任意产品
     */
    public function getAnyItem($ids){

    }

    /**
     * 获取任意活动
     */
    public function getAnyActivity($ids){

    }

    /**
     * 获取任意报告
     */
    public function getAnyReport($ids){

    }
}