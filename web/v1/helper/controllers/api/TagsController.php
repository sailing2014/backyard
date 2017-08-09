<?php
include_once API_PATH.'/ApiController.php';
class TagsController extends ApiController
{
    protected $model;
    protected $tags_type;

    public function __construct() {
        parent::__construct();
        $this->model = $this->model('tags');
        $this->tags_type = $this->model('tags_type');
    }


    /**
     * 通过关键词搜索综合知识
     */
    public function getContentByKeyAction(){

        $key = (int) $this->raw('key');
        $page = (int) $this->raw('page');
        $size = (int) $this->raw('size');
        $page = $page ? $page :1;
        $size = $size ? $size :10;
        $res = $this->model('content_1')->get_content_by_key($key,$page,$size);
        if($res!==false){
            $return = $res;
            $return['status']=array('code'=>200,'message'=>'query success!');
        }else{
            $return['status']=array('code'=>400,'message'=>'query failure!');
        }
        $this->ajaxReturn($return);
    }

    /**
     *获取文章标签
     */
    public function getTagsByContentIdAction($content_id){
        $content_id = (int) $this->raw('content_id');
        $res = $this->model->get_tags_by_content_id($content_id);//获取单篇文章全部标签
        if($res!==false){
            $return  = array(
                'status'=>array('code'=>200,'message'=>'get tags success!'),
                'data'=>$res
            );
        }else{
            $return  = array(
                'status'=>array('code'=>400,'message'=>'get tags failure!')
            );
        }
        $this->ajaxReturn($return);
    }

    /**
     * 获取热门标签分页
     */
    public function getTagsAction(){

        $tags = $this->raw('tags');
        $page = $this->raw('page');
        $size = $this->raw('size');

        $page = $page ? $page :1;
        $size = $size ? $size :10;
        $res = $this->model->get_tags($tags,$page,$size);//获取全部标签
        if($res!==false){
            $return  =array(
                'status'=>array('code'=>200,'message'=>'get tags success!'),
                'page'=>$page,
                'size'=>$size
            );
            $return = array_merge($return,$res);
        }else{
            $return  = array(
                'status'=>array('code'=>400,'message'=>'get tags failure!')
            );
        }
        $this->ajaxReturn($return);
    }

    /**
     * 根据标签id获取文章列表
     */
    public function getContentByTagsIdAction(){
        $tags_id = $this->raw('tags_id');
        $page = $this->raw('page');
        $size = $this->raw('size');
        $page = $page ? $page :1;
        $size = $size ? $size :10;
        $res = $this->model->get_content_by_tags_id($tags_id,$page,$size,-1);
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
     * 根据标签名称获取文章列表
     */
    public function getContentByTagsAction(){
        $tags = $this->raw('tags');
        $page = $this->raw('page');
        $size = $this->raw('size');
        $page = $page ? $page :1;
        $size = $size ? $size :10;
        $res = $this->model->get_content_by_tags($tags,$page,$size);
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
     * 获取标签点击数
     * @return int
     */
    public function  getHitsOfTagsAction(){
        $tags_id = (int)$this->raw('tags_id');
        $auto = (int)$this->raw('auto');//是否自增
        $hits = $this->model->auto_inc($tags_id,$auto);
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

    /**
     * 获取标签所有分类
     * @return array
     */
    public function getAllTypeAction(){
        $res = $this->tags_type->get_cache();
        if($res!==false){
            $return  =array(
                'status'=>array('code'=>200,'message'=>'get types success!'),
                'data' =>$res
            );
        }else{
            $return['status']=array('code'=>400,'message'=>'get types failure!');
        }
        $this->ajaxReturn($return);
    }

    /**
     * 获取某一类型标签
     */
    public function getTagsByTypeIdAction($type_id,$num){
        $type_id = $this->raw('type_id');
        $num = $this->raw('num');
        $where =array("type_id = $type_id");
        $res = $this->model->get_all($where, 1, $num);
        if($res!==false){
            $return  =array(
                'status'=>array('code'=>200,'message'=>'get tags success!'),
                'data' =>$res
            );
        }else{
            $return['status']=array('code'=>400,'message'=>'get tags failure!');
        }
        $this->ajaxReturn($return);
    }

    /**
     * 获取各类热门标签
     */
    public function getHotTagsAction(){
        $num = $this->raw('num');
        $num = is_numeric($num) ? $num : 10;
        $types = $this->tags_type->get_cache();
        foreach($types as $k=>$v){
            $one = $this->model->get_all(array("type_id = $k"), 1, $num);
            $result[] = array(
                'type' => array('type_id'=>$k,'name'=>$v),
                'list' => $one['data']);
        }
        //var_dump($result);
        if($result!==false){
            $return  =array(
                'status'=>array('code'=>200,'message'=>'get tags success!'),
                'data' =>$result
            );
        }else{
            $return['status']=array('code'=>400,'message'=>'get tags failure!');
        }
        $this->ajaxReturn($return);
    }

}