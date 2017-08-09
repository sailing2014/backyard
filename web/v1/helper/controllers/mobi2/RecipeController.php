<?php
include_once NEW_MOB_PATH.'/MobiController.php';

class RecipeController extends MobiController {

    protected $foodModel;
    public function __construct()
    {
        parent::__construct();
        $this->foodModel = new \Content_1_foodModel();
    }

   public function IndexAction(){       
        $month = $this->getMonth();
        $this->view->assign('month',$month);
        $this->view->display('mobi2/recipe/index');
    }
   
    public function getDataAction(){
        
       $data = $this->foodModel->get_all_data();   
       $this->ajaxReturn($data);
    }
  
    public function getContentAction(){
        $id = $this->get('id');
        $type = $this->get('type');
        $index = $this->get('index');
        $this->view->assign('index',$index);
        $this->view->assign('type',$type);
        if($id){
            $content = $this->foodModel->get_by_id($id);
            $this->view->assign("content",$content);
        }        
        $this->view->display('mobi2/recipe/content');
    }
    
    public function getListByTypeAction(){        
        $type = $this->get('type'); 
        $read_id = $this->get('read_id');
        $month = $this->getTypeMonth();       
        $data = array();
        if($type){
            $data = $this->foodModel->get_data_by_foodType($type,$month,$read_id);
        }
        $this->ajaxReturn($data);
    }
    
    public function getMoreByTypeAction(){
        $month = $this->getTypeMonth();
        $type = $this->get('type');
        $this->view->assign('type',$type);
        $title = $this->foodModel->get_title_by_food_type($type);      
        $this->view->assign('title',$title);
        $data = array();
        if($type){                  
            $data = $this->foodModel->get_data_by_foodType($type,$month,"",1,10);
        }            
        $this->view->assign('data',$data);
        $this->view->display('mobi2/recipe/list');
    }
    
    protected function getTypeMonth(){        
        $month = $this->getMonth();
        $index = $this->get('index');
        $this->view->assign('index',$index);
         if($index == 1 ){
            $month = 36;
        }else if( $index == 2){
            $month = 37;
        }else{ //$index =0;
            if($month >12 ){
                $month = 0;
            }
        }
        return $month;
    }
    

}