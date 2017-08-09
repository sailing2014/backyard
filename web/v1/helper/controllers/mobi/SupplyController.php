<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/7/16
 * Time: 13:37
 */
include_once MOB_PATH.'/MobiController.php';
class SupplyController extends MobiController{
    protected $months;
    protected $categoryModel;
    protected $supplyModel;
    protected $topicModel;
    protected $catid;
    public function __construct()
    {
        if($_GET['a']!='async') {
            parent::__construct();
            $this->months = array(
                array('start'=>-1,'close'=>0,'title'=>'待产包'),
                array('start'=>0,'close'=>3,'title'=>'0-3个月'),
                array('start'=>4,'close'=>6,'title'=>'4-6个月'),
                array('start'=>7,'close'=>9,'title'=>'7-9个月'),
                array('start'=>10,'close'=>12,'title'=>'10-12个月'),
            );
            $this->categoryModel = new \CategoryModel();

            $this->topicModel = new \Content_1_topicModel();

            $api_list = \Config::get('api_list');
            $this->catid = $api_list['category']['supply'];
        }
        $this->supplyModel = new \Content_1_supplyModel();
    }

    public function IndexAction(){

        $start = $this->get('start');
        $close = $this->get('close');        
        $range = $this->getRange($start, $close);
        $start = $range["start"];
        $close = $range["close"];
        
//        if($start || $close){
//            $map[1] = "start <= $close";
//            $map[2] = "close >= $start";
//        }

        //用品类目（吃\穿\住\行\用\玩\妈咪专用）
//        $parent =$this->categoryModel->getAll('parentid='.$this->catid,'','catid,catname,start,close');
//        foreach($parent as $k=>$v){
//            $map[3] = "parentid={$v['catid']}";
//            $where = join(' and ',$map);
////            var_dump($where."\n");
//            $parent[$k]['child'] = $this->categoryModel->getAll($where,'','catid,catname,start,close');
//        }
////        dump($parent);
//        $this->view->assign('types',$parent);

//        //收藏
//        $res2 = $this->supplyModel->get_pick($this->user_id);
//        $this->view->assign('pick_count',$res2['count']);

        //当前月龄专题            
        //$res3 = $this->topicModel->get_all("start<=$close and close>=$start",1,2);
        //$this->view->assign('topic_list',$res3['data']);

        $this->view->assign('start',$start);
        $this->view->assign('close',$close);
        //$this->view->assign('months',$this->months);
        $this->view->assign('month',array('start'=>$start,'close'=>$close));
        $this->view->display('mobi/supply/index');
    }

    /**
     * 异步获取用品类目（吃\穿\住\行\用\玩\妈咪专用）
     */
    
    public function TypeAction(){
        $start = $this->get('start');
        $close = $this->get('close');        
        $range = $this->getRange($start, $close);
        $start = $range["start"];
        $close = $range["close"];
        
        if($start || $close){
            $map[1] = "start <= $close";
            $map[2] = "close >= $start";
        }
        //用品类目（吃\穿\住\行\用\玩\妈咪专用）     
        $parent =$this->categoryModel->getAll('parentid='.$this->catid,'','catid,catname,image,start,close',"listorder asc");
        foreach($parent as $k=>$v){
            $map[3] = "parentid={$v['catid']}";
            $where = join(' and ',$map);
//            var_dump($where."\n");
            $parent[$k]['child'] = $this->categoryModel->getAll($where,'','catid,catname,image,start,close');
        }     
        
        $this->ajaxReturn($parent);

    }
    
    
    /**
     * 异步获取当前月龄用品专题
     */
    public function TopicAction(){
        $start = $this->get('start');
        $close = $this->get('close');
        $range = $this->getRange($start, $close);
        $start = $range["start"];
        $close = $range["close"];
        
        $res = $this->topicModel->get_all("start<=$close and close>=$start",1,2);
        if($res){
            $ret = array('code'=>1,'info'=>'get success','data'=>$res);
        }else{
            $ret = array('code'=>0,'info'=>'get failure');
        }
        $this->ajaxReturn($ret);
    }

    public function ListAction(){
        $type_id = $this->get('type_id');

        $start = $this->get('start');
        $start = $start ? $start : 0;
        $close = $this->get('close');
        $close = $close ? $close : 0;
        
        $range = $this->getRange($start, $close);
        $start = $range["start"];
        $close = $range["close"];
        
        $page = $this->get('page');
        $page = $page ? $page : 1;

        $size = $this->get('size');
        $size = $size ? $size :10;

        //获取类目
        $type = $this->categoryModel->getOne("catid=$type_id","","catname");
        $this->view->assign('type',$type);
        
        //获取类目对应在用品推荐栏目对应标题的文章内容
        $content = $this->supplyModel->get_special_by_title($type["catname"]);
        $this->view->assign('content',$content);
        
        //获取当前分类对应月龄的用品
        $res = $this->supplyModel->get_list_by_month($this->user_id,$type_id,$start,$close,$page,$size);
        if($page ==1){
            $this->view->assign('list',$res);
            //个人收藏
            $res2 = $this->supplyModel->get_pick($this->user_id);
            $this->view->assign('pick_count',$res2['count']);
            $this->view->assign('param',array('type_id'=>$type_id,'start'=>$start,'close'=>$close));
            $this->view->display('mobi/supply/list');
        }else{
            $this->ajaxReturn($res);
        }
    }

    public function MineAction(){

        $page = $this->get('page');
        $size = $this->get('size');
        $page = $page ? $page :1;
        $size = $size ? $size :10;

        $res = $this->supplyModel->get_pick($this->user_id,$page,$size);
        if($page ==1){
            $this->view->assign('list',$res);
            $this->view->display('mobi/supply/mine');
        }else{
            $this->ajaxReturn($res);
        }
    }

    /**
     * 收藏当前用品
     */
    public function PickAction(){
//        var_dump($this->user_id);
        $id = $this->get('id');
        if($id){
            $res = $this->supplyModel->pick($this->user_id,$id);
            if($res == -1){
                $ret = array('code'=>-1,'info'=>'repeat');
            }elseif($res>0){
                $ret = array('code'=>1,'info'=>'success');
            }else{
                $ret = array('code'=>0, 'info'=>'failure');
            }
        }else{
            $ret = array('code'=>0, 'info'=>'param error');
        }
        $this->ajaxReturn($ret);
    }

    /**
     * API::异步获取当前用户收藏清单
     */
    public function AsyncAction(){
        $uid = $this->raw('uid');
        $uid = $uid==null ? 0 :$uid ;
        $res = $this->supplyModel->get_pick($uid,1,1);
        if($res){
            $ret = array('status'=>'200','message'=>'success','data'=>$res['count']);
        }else{
            $ret = array('status'=>'400','message'=>'failure');
        }
        $this->ajaxReturn($ret);
    }       
}