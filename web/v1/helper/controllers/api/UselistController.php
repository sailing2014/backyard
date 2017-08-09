<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/7/15
 * Time: 16:03
 */
include_once API_PATH.'/ApiController.php';
class UselistController  extends ApiController {

    protected $model;

    public function __construct() {
        parent::__construct();
        $this->model = $this->model('content_1_uselist');
    }

    public function get_randAction(){
        $month = $this->raw('month');
        $month = $month ? $month : 0;
        $res = $this->model->get_rand($month);
        if($res!==false){
            $ret['status'] = array('code'=>200,'message'=>'get success');
            $ret['data'] = $res;
        }else{
            $ret['status'] = array('code'=>400,'message'=>'get failure');
        }
        $this->ajaxReturn($ret);
    }

}