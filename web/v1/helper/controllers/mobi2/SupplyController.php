<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/7/16
 * Time: 13:37
 */
include_once NEW_MOB_PATH.'/MobiController.php';
class SupplyController extends MobiController{    
    
    protected $tagsTypeModel;
    protected $mixTypeModel;
    
    public function __construct()
    {        
        parent::__construct();            
        $this->tagsTypeModel = new \Tags_typeModel();
        $this->mixTypeModel = new \Mix_typeModel();
    }

    public function IndexAction(){       
        $tagsType = $this->tagsTypeModel->get_all();
        $this->view->assign("tagsType",$tagsType);
        $data = "";
        if($tagsType["count"]){
            $month = $this->getMonth();
            $data = $this->mixTypeModel->get_content_list_by_type_id($tagsType["data"][0]["id"], $month);
            foreach ($data["data"] as $key=>$val){
                $data["data"][$key]["tag"] = preg_split("/\s*(,|，)\s*/", $val["tag"]);
            }
        }
        $this->view->assign("data",$data);
        $this->view->display('mobi2/supply/index');
    }
       
    public function getContentAction(){
        $id = $this->get('id');      
        if($id){
            $content = $this->mixTypeModel->get_by_id($id);
            if($content[0]["tag"]){
                $content[0]["tag"] = preg_split("/\s*(,|，)\s*/", $content[0]["tag"]);
            }            
            $this->view->assign("content",$content);
            $this->assignTitle();
        }        
        $this->view->display('mobi2/supply/content');
    }
    
    public function getListByTypeIdAction(){
        $type_id  = $this->get('type_id');
        $read_id = $this->get('read_id');
        $page = $this->get('page');
        $page = empty($page)?1:$page;
        $size = $this->get('size');
        $size = empty($size)?3:$size;
        $data ="";
        if($type_id){
            $month = $this->getMonth();
            $data = $this->mixTypeModel->get_content_list_by_type_id($type_id,$month,$read_id,$page,$size);         
            for($i=0;$i<count($data["data"]);$i++){                
                $data["data"][$i]["tag"] = preg_split("/\s*(,|，)\s*/", $data["data"][$i]["tag"]);
            }
        }          
        $this->ajaxReturn($data);
    }
    
    public function getListByTagAction(){        
        $this->assignTitle();
        $tag = $this->get('tag');
        $data = "";
        if($tag){
            $month = $this->getMonth();
            $data = $this->mixTypeModel->get_list_by_tag($tag, $month);
            foreach($data as $key=>$val){
                $data[$key]["tag"] = preg_split("/\s*(,|，)\s*/", $val["tag"]);
            }
        }    
        $this->view->assign("data",$data);
        $this->view->display('mobi2/supply/list');
    }
    
    protected function assignTitle(){
        $type_id = $this->get('type_id');
        $this->view->assign('type_id',$type_id);
        if($type_id){
            $title = $this->tagsTypeModel->getOne('id='.$type_id,"","name");
            $titlename = isset($title["name"])?$title["name"]."专题":"育儿知识";     
            $this->view->assign('title',$titlename);
        }
    }
}