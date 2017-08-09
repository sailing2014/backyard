<?php
class TagsController extends Admin
{
    protected $model;
    protected $types;
    public function __construct()
    {
        parent::__construct();
        $this->model = $this->model('tags');
        //$this->model('tags_type')->clr_cache();
        $this->types = $this->model('tags_type')->get_cache();
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
        //dump($this->types);
        $type = $this->get('type');
        $tags = $this->get('tags');
        $page = (int)$this->get('page');
        $size = $this->get('size');

        is_numeric($type) && $where[] = "type_id = $type";
        $tags && $where[] = "name like '%$tags%'";
        $page = $page ? $page :1;
        $size = $size ? $size :10;
        $result = $this->model->get_all($where,$page,$size);
        $result['page']=$page;
        $result['size']=$size;
        $pagelist = $this->instance('pagelist');
        $pagelist->loadconfig();
        $result['pagelist'] =  $pagelist->total($result['count'])->url(url('admin/tags/index',array('type'=>$type,'tags'=>$tags,'page'=>'{page}')))->num($result['size'])->page($result['page'])->output();
        //dump($result);
        $this->view->assign('data',$result);
        $this->view->assign('past',array('type'=>$type,'tags'=>$tags));
        $this->view->assign('types',$this->types);
        $this->view->display('admin/tags/all');
    }

    /**
     *
     */
    public function oneAction()
    {
        $id = $this->get('id');
        if($this->is_post()){
            $data = $_POST;
            $name = trim($data['name']);           
            if($id){
                $where = "name='$name' and id!=$id";
            }else{
                $where = "name='$name'";
            }
            $count = $this->model->count($where);
            if($count){
                $return = array('code'=>-1,'info'=>'标签重复');
            }else{
                if($name){
                    $result = $this->model->set_one($id,$data);
                    if($result){
                        $return = array('code'=>1,'info'=>'保存成功');
                    }else{
                        $return = array('code'=>0,'info'=>'保存失败');
                    }
                }else{
                    $return = array('code'=>0,'info'=>'标签没有内容哦~');
                }
            }
            die(json_encode($return));
        }else{
            if($id){
                $result = $this->model->get_one($id);
                $this->view->assign('data',$result);
            }else{

            }
            $this->view->assign('types',$this->types);
            $this->view->display('admin/tags/one');
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
        $status && $set = array('status'=>$status);

        $type_id = (int)$this->get('type_id');
        $type_id && $set = array('type_id'=>$type_id);

        $result = $this->model->set_all($set,$ids);
        if($result){
            $return = array('code'=>1,'info'=>'操作成功');
        }else{
            $return = array('code'=>0,'info'=>'操作失败');
        }
        exit(json_encode($return));
    }
}