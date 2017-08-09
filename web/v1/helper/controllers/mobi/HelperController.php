<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/7/16
 * Time: 13:37
 */
include_once MOB_PATH.'/MobiController.php';
class HelperController extends MobiController{
    public function __construct()
    {
        parent::__construct();
    }

    public function IndexAction(){
//        dump($this->user_info);
        extract($this->user_info);
        
        empty($bind_air_cleaner) and $bind_air_cleaner = 0; //是否绑定空气净化器
        if($entity_id){
            //*****设备信息(是否绑定空气净化器)*****
            $url_1 = $this->item('app_home','get_device_list_by_entity');
            $param_1 = $this->param;
            $param_1['entity_ids'] = $entity_id;
            $res_1 = $this->httpclient->post($url_1,$param_1);
//            dump($res_1);
            if($res_1['status']['code']==200){
                foreach($res_1['entity_devices'][$entity_id] as $v){
//                    echo $v['device_type'];
                    if($v['device_type']==172){
                        $bind_air_cleaner = 1;//存在空气净化器
                        break;
                    }
                }
            }
        }
       
        $this->view->assign('bind_air_cleaner',$bind_air_cleaner);       
       
        //获取是否为真宝宝,真宝宝flag=1
        $flag = $entity["flag"];
        $this->view->assign('flag',$flag);  
        
        //获取宝宝第几周
        $week = ceil($this->days/7);
        $week = $week ? $week: 1;
        $this->view->assign('week',$week);
        $this->view->assign('month',$this->month);
        
        //获取知识标签分类
        $res_1 = $this->model('tags_type')->get_cache();
        $this->view->assign('tagsTypeData',$res_1);
        $this->view->assign('devicetype',$devicetype);
        $this->view->display('mobi/helper/index');
    }

     public function ListAction(){
        $type_id = $this->get('type_id');
        $tags_id = $this->get('tags_id');
        $month = $this->month;
        //当前分类信息
//        $res_1 = $this->model('tags_type')->get_one($type_id);
//        $this->view->assign('tagsType',$res_1['name']);

        //标签列表
        $res_2 = $this->model('tags')->get_all(array("type_id=$type_id"),1,60);
        $this->view->assign('tagsData',$res_2['data']);
        $tags_id or $tags_id = $res_2['data'][0]['id'];
        //当前标签对应文章
        if($tags_id){//如果存在tags_id，那么按照tags_id查询，否则按照type_id查询
            $res_1 = $this->model('tags')->get_content_by_tags_id($tags_id,1,10,$month);
        }else{//当前分类对应文章
            $res_1 = $this->model('content_1_mix')->get_all_by_type($type_id,"title,thumb,inputtime",1,10,$month);
        }
//        dump($res_1);
        if($res_1){

        }else{
            $ret['status']=array('code'=>400,'message'=>'get failure');
        }
        foreach($res_1['data'] as &$v){
            $v['inputtime'] = date('Y-m-d',$v['inputtime']);
        }
        unset($v);

//        dump($contentData);
        $type_title = $this->model('tags_type')->get_cache();
        $this->view->assign('type_title',$type_title[$type_id]);
        $this->view->assign('contentData',$res_1['data']);
        $this->view->assign('type_id',$type_id);
        $this->view->assign('tags_id',$tags_id);

        $this->view->display('mobi/helper/list');
    }


    /**
     * 异步数据
     */
    public function MoreAction(){
        $type_id = $this->get('type_id');
        $tags_id = $this->get('tags_id');
        $month = $this->month;
        
        $page = $this->get('page');
        $page = $page ? $page :1;
        $size = $this->get('size');
        $size = $size ? $size :10;
        if($tags_id){//如果存在tags_id，那么按照tags_id查询，否则按照type_id查询
            $res = $this->model('tags')->get_content_by_tags_id($tags_id,$page,$size,$month);
        }else{
            $res = $this->model('content_1_mix')->get_all_by_type($type_id,'id,title,thumb,inputtime',$page,$size,$month);
        }
        if($res){
            foreach($res['data'] as &$v){
                $v['inputtime'] = date('Y-m-d',$v['inputtime']);
            }
            unset($v);
            $ret['status']=array('code'=>200,'message'=>'get success');
            $ret['data'] = $res['data'];
        }else{
            $ret['status']=array('code'=>400,'message'=>'get failure');
        }
        $this->ajaxReturn($ret);
    }
    
    /**
     * 异步获取标签集
     */
    public function tagsAction(){
        $data = array();
        $type_id = $this->get('type_id');
        //标签列表
        $res = $this->model('tags')->get_all(array("type_id=$type_id"),1,60);
        $data['tagsData'] = $res['data'];
        $this->ajaxReturn($data);
    }
    
    
    /**
     * 异步标签下的文章
     */
    public function contentAction(){
        $data = array();
        $type_id = $this->get('type_id');
        $tags_id = $this->get('tags_id');
        $month = $this->month;

        //标签列表
        $res_2 = $this->model('tags')->get_all(array("type_id=$type_id"),1,60);
        $tags_id or $tags_id = $res_2['data'][0]['id'];
        //当前标签对应文章
        if($tags_id){//如果存在tags_id，那么按照tags_id查询，否则按照type_id查询
            $res_1 = $this->model('tags')->get_content_by_tags_id($tags_id,1,10,$month);
        }else{//当前分类对应文章
            $res_1 = $this->model('content_1_mix')->get_all_by_type($type_id,"title,thumb,inputtime",1,10,$month);
        }
//        dump($res_1);
            foreach($res_1['data'] as &$v){
                $v['inputtime'] = date('Y-m-d',$v['inputtime']);
            }
            unset($v);              
      
        $data['contentData'] = $res_1['data'];
        $data['type_id'] = $type_id;
        $data['tags_id'] = $tags_id;
        $this->ajaxReturn($data);
    }
}