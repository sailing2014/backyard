<?php

include_once NEW_MOB_PATH.'/MobiController.php';

class GrowController extends MobiController {

    protected $growModel;
    public function __construct()
    {
        parent::__construct();
        $this->growModel = new \Content_1_growModel();
    }

    public function indexAction(){       
        $week = ceil($this->days/7);//宝宝第几周
        $week = $week ? $week: 1;
        
        $month = $this->getMonth();
        if($month > 12){
            $month = 12;
            $week = 52;
        }
        $this->view->assign("week",$week);
        $this->view->assign("month",$month);       
        $this->view->display('mobi2/grow/index');        
    }
    
    public function getTitleAction(){    
        $data = $this->growModel->get_title();         
        $this->ajaxReturn($data);
    }
    
    public function getContentAction(){     
        $id = $this->get("id");
        $data = $this->growModel->get_content($id);
        if($data){
            foreach ($data as $key=>$value) {
                $data[$key]["content"] = html_entity_decode($value['content']);
            }
        }     
        $this->ajaxReturn($data);
    } 
   
  

}