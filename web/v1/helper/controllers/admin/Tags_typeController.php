<?php
class Tags_typeController extends Admin
{
    protected $model;
    protected $types;
    public function __construct()
    {
        parent::__construct();
        $this->model = $this->model('Tags_type');
    }

    /**
     *
     */
    public function indexAction()
    {
        $this->allAction();
    }

    /**
     *
     */
    public function allAction(){
        $type = $this->get('type');
        $page = (int)$this->get('page');
        $size = $this->get('size');
        $where = is_numeric($type) ? "status = $type" : "";
        $page = $page ? $page :1;
        $size = $size ? $size :10;
        $result = $this->model->get_all($where,$page,$size);
        $result['page']=$page;
        $result['size']=$size;
        $pagelist = $this->instance('pagelist');
        $pagelist->loadconfig();
        $result['pagelist'] =  $pagelist->total($result['count'])->url(url('admin/tags_type/index',array('page'=>'{page}')))->num($result['size'])->page($result['page'])->output();
        //dump($result);
        $this->view->assign('data',$result);
        $this->view->assign('status',$this->model->status);
        is_numeric($type) && $this->view->assign('map',array('type'=>$type));
        $this->view->display('admin/tags_type/all');
    }

    /**
     *
     */
    public function oneAction()
    {
        $id = $this->get('id');
        if($this->is_post()){//提交
            $data = $_POST;//dump($data);
            $result = $this->model->set_one($id,$data);
            if($result){
                $return = array('code'=>1,'info'=>'保存成功');
            }else{
                $return = array('code'=>0,'info'=>'保存失败');
            }
            die(json_encode($return));
        }else{
            if($id){
                $result = $this->model->get_one($id);
                $this->view->assign('data',$result);
            }else{

            }
            $this->view->assign('status',$this->model->status);
            $this->view->display('admin/tags_type/one');
        }
    }

    /**
     *
     */
    public function delAction(){
        $ids = $this->get('ids');
        $result = $this->model->delete("id in($ids)");
        if($result){
            $return = array('code'=>1,'info'=>'删除成功');
        }else{
            $return = array('code'=>1,'info'=>'删除失败');
        }
        exit(json_encode($return));
    }

    /**
     *
     */
    public function setAction(){
        $ids = $this->get('ids');
        $status =(int)$this->get('status');
        $set = array('status'=>$status);
        $result = $this->model->set_all($set,$ids);
        if($result){
            $return = array('code'=>1,'info'=>'操作成功');
        }else{
            $return = array('code'=>0,'info'=>'操作失败');
        }
        exit(json_encode($return));
    }

    public function clrAction(){
        $result = $this->model->clr_cache();
        if($result){
            $return = array('code'=>1,'info'=>'操作成功');
        }else{
            $return = array('code'=>0,'info'=>'操作失败');
        }
        exit(json_encode($return));
    }
}