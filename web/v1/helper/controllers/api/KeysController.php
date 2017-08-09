<?php
include_once API_PATH.'/ApiController.php';
class KeysController extends ApiController
{
    protected $model;

    public function __construct() {
        parent::__construct();
        $this->model = $this->model('keys');
    }


    /**
     * 通过关键词搜索综合知识
     */
    public function getContentByKeysAction(){
        $keys = $this->raw('keys');
        $page = (int) $this->raw('page');
        $size = (int) $this->raw('size');
        $page = $page ? $page :1;
        $size = $size ? $size :10;
        $res = $this->model->get_content_by_keys($keys,$page,$size);
//        var_dump($res);
        if($res!==false){
            $return  =array(
                'status'=>array('code'=>200,'message'=>'get content success!'),
                'page'=>$page,
                'size'=>$size
            );
            $return = array_merge($return,$res);
        }else{
            $return['status']=array('code'=>400,'message'=>'get content failure!');
        }
        $this->ajaxReturn($return);
    }

    /**
     * 获取热门关键词分页
     */
    public function getKeysAction(){
        $page = $this->raw('page');
        $size = $this->raw('size');
        $page = $page ? $page :1;
        $size = $size ? $size :10;
        $res = $this->model->get_keys($page,$size);//获取全部标签
        if($res!==false){
            $return  =array(
                'status'=>array('code'=>200,'message'=>'get keys success!'),
                'page'=>$page,
                'size'=>$size
            );
            $return = array_merge($return,$res);
        }else{
            $return  = array(
                'status'=>array('code'=>400,'message'=>'get keys failure!')
            );
        }
        $this->ajaxReturn($return);
    }

    /**
     * 获取关键字搜索次数
     * @return int
     */
    public function  getHitsOfKeysAction(){
        $keys_id = (int)$this->raw('keys_id');
        $auto = (int)$this->raw('auto');//是否自增
        $hits = $this->model->auto_inc($keys_id,$auto);
        if($hits!==false){
            $return  =array(
                'status'=>array('code'=>200,'message'=>'get hits success!'),
                'data' =>$hits
            );
        }else{
            $return['status']=array('code'=>400,'message'=>'get hits failure!');
        }
        $this->ajaxReturn($return);
    }


}