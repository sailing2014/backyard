<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 8/6 0006
 * Time: 14:44
 */

include_once MOB_PATH.'/MobiController.php';
class SpecialController  extends MobiController{
    protected $specialModel;
    public function __construct()
    {
        parent::__construct();
        $this->specialModel=new \Content_1_specialModel();
    }

    public function ListAction(){
        $page = $this->get('page');
        $size = $this->get('size');
        $page = $page ?$page :1;
        $size = $size ?$size :10;
        $res = $this->specialModel->get_all($page,$size);
        $this->view->assign('list',$res);

        $supplyModel = new \Content_1_supplyModel();
        $res2 = $supplyModel->get_pick($this->user_id);
        $this->view->assign('pick_count',$res2['count']);

        $this->view->display('mobi/special/list');
    }

    public function ViewAction(){
        $id = $this->get('id');
        $res = $this->specialModel->get_one($id,$this->user_id);
        $this->view->assign('view',$res[0]);

        $supplyModel = new \Content_1_supplyModel();
        $res2 = $supplyModel->get_pick($this->user_id);
        $this->view->assign('pick_count',$res2['count']);

        $this->view->display('mobi/special/view');
    }
}