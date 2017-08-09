<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/12/14
 * Time: 13:37
 */
include_once NEW_MOB_PATH.'/MobiController.php';
class SearchController extends MobiController{    
    
    protected $newKeysModel;
    
    public function __construct()
    {
        
        parent::__construct();            
        $this->newKeysModel = new \Keys_newModel();
    }

    public function IndexAction(){       
        $this->view->display('mobi2/search/index');
    }
   
    public function getListbyKeyAction(){
        $key = $this->get("key");
        $this->view->assign("key",$key);
        $month = $this->getMonth();
        $data = $this->newKeysModel->get_data_by_key($key,$month);
        $this->view->assign("data",$data);
        $this->view->display('mobi2/search/list');
    }
    
    public function addKeyAction(){
        $key = trim($this->get("key"));
        $limit = $this->get("limit");
        $limit = $limit? $limit : 15;
        $uid = $this->user_id;
        if($key){
            $ret = $this->newKeysModel->add_user_key($uid, $key,$limit);  
            $this->ajaxReturn(array("ret"=>$ret));
        }else{
            $this->ajaxReturn(array("ret"=>false));
        }
    }
    public function getUserKeyAction(){
        $uid = $this->user_id;
        $data = $this->newKeysModel->get_user_key($uid);        
        $this->ajaxReturn($data);
    }
    
}