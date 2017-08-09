<?php
include_once CONTROLLER_DIR . 'admin/Common.php';

class MobiController extends Common
{
    protected $httpclient;
    protected $param;
    protected static $api_list;

    public $user_info;
    public $user_id;
    public $baby_id;
    public $month;
    public $days;
    public $y;
    public $d;
    
public function __construct($ischeckuser=true) {

        parent::__construct();

        define('MOBI_PATH', 'views/mobi2');

        $this->httpclient = $this->instance('http_client');
        $api_list = \Config::get('api_list');
        static::$api_list = $api_list;
        //dump($api_list['api']);
        $time = time();
        $this->param = array(
            'api_key' => $api_list['api']['api_key'],
            'api_token' => sha1($api_list['api']['api_secret'] . $time),
            'time' => $time
        );        
        $this->view->setTheme('');
        //dump($this->param);

        if($ischeckuser) {
            $uid = $_REQUEST['uid'];
            $token = $_REQUEST['token'];
            $entity_id = $_REQUEST['entity_id'];

            //win10 APP 专用，没有用户登录信息 	
            $win10 = $_REQUEST['special'];

            if ($uid && $token && $entity_id) {
                //TODO : 验证uid 和token
                //*****宝宝信息*****
                $param_0 = $this->param;
                $param_0['uid'] = $uid;
                $param_0['token'] = $token;
                $param_0['entity_id'] = $entity_id;

                $url_0 = $this->item('app_home', 'get_entity');  
                $res_0 = $this->httpclient->post($url_0, $param_0);
        //        dump($_SERVER['QUERY_STRING']);
                if ($res_0['_status']['_code'] == 200) {
                    $res_0['entity']['avatar'] = $res_0['data']['avatar'];
                    $res_0['entity']['growup'] = getGrowup($res_0['data']['birthday']);//天数
                    $res_0['entity']['zodiac'] = getZodiac($res_0['data']['birthday']);//星座
                    $res_0['entity']['animal'] = getAnimal($res_0['data']['birthday']);//生肖
                    $entity = $res_0['entity'];
                } else {
                    //认证失败
                    //$this->ajaxReturn(array('code'=>0,'info'=>'login failure'));
                    $this->alertAction('login failure');
                }
                $user_info = compact('uid', 'token', 'entity_id', 'entity');
        //        $_SESSION = $user_info;//保存
                setcookie('user_info',json_encode($user_info),time()+86400);
             }else if($win10){//针对win10假宝宝
                $user_info = array(
                                    "uid"=> "10000760",
                                    "token"=> "sess::e193dcf55aca8ac6648095e9fda1e083",
                                    "entity_id"=> "832",
                                    "entity"=> array(
                                                    "id"=> "832",
                                                    "uid"=> "10000760",
                                                    "name"=> "宝宝",
                                                    "birthday"=> "2015-07-05",
                                                    "gender"=> "1",
                                                    "created_at"=> "2015-07-15 02:42:01",
                                                    "is_default"=> "1",
                                                    "birth_time"=> "00:00",
                                                    "blood_type"=> "0",
                                                    "flag"=> "2",
                                                    "avatar"=> "",
                                                    "cover"=> "",
                                                    "growup"=> array(
                                                                    "y"=> 0,
                                                                    "m"=> 5,
                                                                    "d"=> 5,
                                                                    "days"=> 158
                                                    ),
                                                    "zodiac"=> "巨蟹座",
                                                    "animal"=> "羊"
                                                 )
                                  );
                setcookie('user_info',json_encode($user_info),time()+86400);
             }else {

                //app验证登录
        //        $user_info = $_SESSION;
                $user_info = json_decode($_COOKIE['user_info'],true);
                if (empty($user_info)) {
                    //TODO 提示需要重新登录
        //            $this->ajaxReturn(array('code' => 0, 'info' => 'please login again'));
                    $this->alertAction('please login again');
                }
            }

//                var_dump($user_info);
            $this->user_info = $user_info;
            $this->user_id = $user_info['uid'];
            $this->baby_id = $user_info['entity_id'];
        //        dump($entity);
            $this->month = $user_info['entity']['growup']['m'];
            $this->days = $user_info['entity']['growup']['days'];
            $this->d = $user_info['entity']['growup']['d'];
            $this->y = $user_info['entity']['growup']['y'];
        }
//        var_dump($this->month);
    }

    /**
     * @param $param
     * @return mixed
     */
    public function raw($param)
    {
        //$post = $GLOBALS['HTTP_RAW_POST_DATA'];//raw
        $post = file_get_contents("php://input");//raw
        $post = json_decode($post, true);
        return $post[$param];
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

    /**
     * 获取任意数据
     */
    public function getAny($ids, $field, $model, $action)
    {
        $url = $this->item('tryout');
        $param['model'] = $model;
        $param['action'] = $action;
        $param['doc_id'] = $ids;
        $result = $this->httpclient->post($url, $param);
        if ($result['_status']['_code'] == 200) {
            foreach ($result['_data'] as $k => $v) {

            }
        }

    }

    /**
     * 获取任意产品
     */
    public function getAnyItem($ids)
    {

    }

    /**
     * 获取任意活动
     */
    public function getAnyActivity($ids)
    {

    }

    /**
     * 获取任意报告
     */
    public function getAnyReport($ids)
    {

    }


    public function alertAction($message){
        $this->view->assign('message',$message);
        $this->view->display('mobi/notice/index');
        die;
    }
    
  
    public function getMonth(){
       $year = $this->y;
       $ret = $this->month;
       
       if($year){
        $ret = $year * 12 + 1; 
       }
       
       return $ret;
   }
}