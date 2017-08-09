<?php
include_once CONTROLLER_DIR.'Cloud.php';
class Blog_tagsController extends Cloud
{

    public function __construct()
    {
        parent::__construct();

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
    public function allAction()
    {
        $page = $this->get('page');
        $size = $this->get('size');

        $page = $page ? $page :1;
        $size = $size ? $size :10;

        $url = $this->item('blog_tags','get_all');
        $param = $this->param;
        $param['page']= $page;
        $param['size']= $size;
        $result = $this->httpclient->post($url,$param);
//        dump($result);
        if($result['status']['code']==200){

            $result['page']=$page;
            $result['size']=$size;
            $pagelist = $this->instance('pagelist');
            $pagelist->loadconfig();
            $result['pagelist'] =  $pagelist->total($result['count'])->url(url('admin/blog_tags/index',array('page'=>'{page}')))->num($result['size'])->page($result['page'])->output();

        }else{

        }

        $this->view->assign('data',$result);
        $this->view->display('admin/blog_tags/all');
    }


    /**
     *
     */
    public function oneAction()
    {
        $id = $this->get('id');
        $param = $this->param;
        if($this->is_post()){//添加或者修改

            $url =$this->item('blog_tags','set_one');
            $param['id']= (int)$id;
            $param['data'] = $_POST;
            $result = $this->httpclient->post($url,$param);
            if ($result['status']['code'] == 200) {
                $return = array('code'=>1,'info'=>'操作成功');
            } else {
                $return = array('code'=>0,'info'=>'操作失败');
            }
            $this->ajaxReturn($return);
        }else{
            if($id){//查看
                $url = $this->item('blog_tags','get_one');
                $param['id'] = (int)$id;
                $param['field'] = "id,define";
                $result = $this->httpclient->post($url,$param);
                if($result['status']['code']==200){
                    $this->view->assign('data', $result['data']);
                }else{

                }
            }else{//添加

            }
            $this->view->display('admin/blog_tags/one');
        }
    }


    /**
     *
     */
    public function delAction(){
        $ids = $this->get('ids');
        $url = $this->item('blog_tags','del_any');
        $param = $this->param;
        $param['ids']= $ids;
        $result = $this->httpclient->post($url,$param);
        if($result['status']['code']==200){
            $return = array('code'=>1,'info'=>'删除成功');
        }else{
            $return = array('code'=>0,'info'=>'删除失败');
        }
        exit(json_encode($return));
    }

    /**
     *
     */
    public function setAction(){
        $ids = $this->get('ids');
        $pid =(int)$this->get('pid');
        $type_id =(int)$this->get('type_id');
        $param = $this->param;
        $param['ids'] = $ids;
        $param['data'] = array('pid'=>$pid,'type_id'=>$type_id);
        $url = $this->item('blog_tags','set_any');
        $result = $this->httpclient->post($url,$param);
        if($result['status']['code']==200){
            $return = array('code'=>1,'info'=>'操作成功');
        }else{
            $return = array('code'=>0,'info'=>'操作失败');
        }
        exit(json_encode($return));
    }

}