<?php
include_once API_PATH.'/ApiController.php';
class FavsController extends ApiController
{
    protected $model;
    protected $tags_type;

    public function __construct() {
        parent::__construct();
        $this->model = $this->model('favs');
    }


    /**
     *获取文章收藏次数
     */
    public function getSumContentAction(){
        $content_id = (int) $this->raw('content_id');
        $res = $this->model->get_count_content($content_id);//获取单篇文章全部标签
        if($res!==false){
            $return  = array(
                'status'=>array('code'=>200,'message'=>'get count success!'),
                'count'=>$res
            );
        }else{
            $return  = array(
                'status'=>array('code'=>400,'message'=>'get count failure!')
            );
        }
        $this->ajaxReturn($return);
    }

    /**
     *获取用户收藏次数
     */
    public function getSumUserAction(){
        $user_id = (int) $this->raw('user_id');
        $res = $this->model->get_count_user($user_id);//获取单篇文章全部标签
        if($res!==false){
            $return  = array(
                'status'=>array('code'=>200,'message'=>'get count success!'),
                'count'=>$res
            );
        }else{
            $return  = array(
                'status'=>array('code'=>400,'message'=>'get count failure!')
            );
        }
        $this->ajaxReturn($return);
    }

    /**
     * 获取收藏分页
     */
    public function getAllAction(){

        $user_id = $this->raw('user_id');
        $page = $this->raw('page');
        $size = $this->raw('size');

        $page = $page ? $page :1;
        $size = $size ? $size :10;
        $res = $this->model->get_all(array('user_id'=>$user_id),$page,$size);//获取全部标签
        if($res!==false){
            $return  =array(
                'status'=>array('code'=>200,'message'=>'get favs success!'),
                'page'=>$page,
                'size'=>$size
            );
            $return = array_merge($return,$res);
        }else{
            $return  = array(
                'status'=>array('code'=>400,'message'=>'get favs failure!')
            );
        }
        $this->ajaxReturn($return);
    }

    /**
     *删除用户收藏
     */
    public function delAnyAction(){
        $user_id = (int)$this->raw('user_id');
        $content_ids = $this->raw('content_ids');
        $res = $this->model->del_any($user_id,$content_ids);
        if($res!==false){
            $return  =array(
                'status'=>array('code'=>200,'message'=>'delete favs success!')
            );
        }else{
            $return  = array(
                'status'=>array('code'=>400,'message'=>'delete favs failure!')
            );
        }
        $this->ajaxReturn($return);
    }

}