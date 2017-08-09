<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/7/9
 * Time: 11:00
 */
include_once NEW_MOB_PATH.'/MobiController.php';
class IndexController extends MobiController {

    public function __construct()
    {
        parent::__construct();        
    }

    public function indexAction(){            
        $this->view->display('mobi2/index/index');
    }

    
    public function getbindCameraAction() {
        $bind_camera = $this->getbindCamera();
        $ret = array('code'=>1,'info'=>'success','data'=>$bind_camera);
        $this->ajaxReturn($ret);
    }
    
    public function getbindCamera() {        
        $bind_camera =  $this->user_info['bind_camera'];//宝宝有无绑定设备
        empty($bind_camera) and $bind_camera = 0; //是否绑定看宝器（摄像头）
        
        $entity_id = $this->user_info['entity_id'];
        if($entity_id){
            //*****设备信息(是否绑定看宝器)*****
            $url_1 = $this->item('app_home','get_device_list_by_entity');
            $param_1 = $this->param;
            $param_1['entity_id'] = $entity_id;
            $res_1 = $this->httpclient->post($url_1,$param_1);
//            dump($res_1);
            if($res_1['_status']['_code']==200){  
                        $bind_camera = 1;
                    }        
        }
        
        return $bind_camera;
    }    

    
    public function getadvertDataAction()
    {
        $bind_camera = $this->getbindCamera();
        if($bind_camera){
            $url_21 = $this->item('app_home','home_carousel');
        }else{
            $url_21 = $this->item('app_home','home_advert');
        }
        $res_21 = $this->httpclient->get($url_21);
        
        $advertData = $res_21['data'];
        $ret = array('code'=>1,'info'=>'success','data'=>$advertData);
        $this->ajaxReturn($ret);
    }    

}