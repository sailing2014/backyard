<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 8/6 0006
 * Time: 14:44
 */

include_once MOB_PATH.'/MobiController.php';
class TopicController  extends MobiController{
    protected $topicModel;
    public function __construct()
    {
        parent::__construct();
        $this->topicModel=new \Content_1_topicModel();
    }

    public function ListAction(){

        $page = $this->get('page');
        $size = $this->get('size');
        $page = $page ?$page :1;
        $size = $size ?$size :2;
        
        $start = $this->get('start');
        $close = $this->get('close');         
        $range = $this->getRange($start, $close);
        $start = $range["start"];
        $close = $range["close"];  
        $res = $this->topicModel->get_all("start<=$close and close>=$start",$page,$size);
        $this->view->assign('start',$start);
        $this->view->assign('close',$close);
        if($page ==1){
            $this->view->assign('list',$res);

//            $supplyModel = new \Content_1_supplyModel();
//            $res2 = $supplyModel->get_pick($this->user_id);
//            $this->view->assign('pick_count',$res2['count']);
            
            $this->view->display('mobi/topic/list');
        }else{
            $this->ajaxReturn($res);
        }
    }

    public function ViewAction(){
        $id = $this->get('id');
        $res = $this->topicModel->get_one($id);
        $this->view->assign('view',$res[0]);

        $supplyModel = new \Content_1_supplyModel();
        $res2 = $supplyModel->get_pick($this->user_id);
        $this->view->assign('pick_count',$res2['count']);

        $this->view->display('mobi/topic/view');
    }
}