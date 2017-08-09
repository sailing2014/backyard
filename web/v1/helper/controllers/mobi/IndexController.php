<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/7/9
 * Time: 11:00
 */
include_once MOB_PATH.'/MobiController.php';
class IndexController extends MobiController {

    public function __construct()
    {
        parent::__construct();
        define('BUCKET', $this->item('alioss_bucket'));
    }

    public function indexAction(){

//        dump($this->user_info);
        extract($this->user_info);

//
//        //如果存在静态且未过期，直接指向静态缓存
//        $now_time = time();//当前时间
//        /*        $preg = "./cache/html/home/$entity_id_*.html";
//                $old_path = glob($preg);
//                if($old_path){
//                    $old_time = (int)substr($old_path[0],-15,10);
//        //            var_dump($preg,$old_path,$old_time);
//                    if( $old_time && $old_time > $now_time ){//还未过期，仍然读取静态缓存
//                        $html = file_get_contents($old_path[0]);
//                        die($html);
//                    }else{
//                        foreach($old_path as $v){
//                            unlink($v);
//                        }
//                    }
//                }*/
//        //缓存静态文件
////        $this->view->cache("app_home_$entity_id",3600);
//        //***********************************
//        $this->view->assign('entityData',$entity);
//
//        //如果存在宝宝，那么显示指标数据、消息提醒、
//        empty($bind_camera) and $bind_camera = 0; //是否绑定看宝器（摄像头）
//        if($entity_id){
//            //*****设备信息(是否绑定看宝器)*****
//            $url_1 = $this->item('app_home','get_device_list_by_entity');
//            $param_1 = $this->param;
//            $param_1['entity_ids'] = $entity_id;
//            $res_1 = $this->httpclient->post($url_1,$param_1);
////            dump($res_1);
//            if($res_1['status']['code']==200){
//                foreach($res_1['entity_devices'][$entity_id] as $v){
////                    echo $v['device_type'];
//                    if($v['device_type']==187){
//                        $bind_camera = 1;//存在看宝器
//                        break;
//                    }
//                }
//            }
//
//            //*****宝宝设备成长指标*****
////            //获取设备实时数据
////            $url_3 = $this->item('app_home','get_all_real_time_data');
////            $param_3 = array(
////                'uid'           => $uid,
////                'token'         => $token,
////                'entity_id'    => $entity_id
////            );
////            $res_3 = $this->httpclient->post($url_3,$param_3);
//////        var_dump($res_3);
////            $sensors = array(
////                '-', '温度','湿度','空气质量','体重','身高','体表体温','心跳'
////            );
////            if($res_3['status']['code']==200){
////                foreach($res_3['data'] as $v){
////                    $v['level'] && in_array($v['type'],array(4,5,6,7)) and $abnormalData[] = sprintf("当前宝宝%s不在正常范围内，请及时查看",$sensors[$v['type']]);
////                    $v['level'] && in_array($v['type'],array(1,2)) and $abnormalData[] = sprintf("当前室内%s不在正常范围内，请及时调整",$sensors[$v['type']]);
////                    $v['level'] && in_array($v['type'],array(3)) and $abnormalData[] = sprintf("当前室内%s不佳，请及时开窗透气",$sensors[$v['type']]);
////                }
////            }else{
////
////            }
////            $this->view->assign('abnormalData',$abnormalData);
//
//
//            //*****重要事情提醒*****
////            $url_22 = $this->item('app_home','get_two_event');
////            $param_22 = array(
////                'uid'           => $uid,
////                'token'         => $token,
////                'entity_id'    => $entity_id,
////                'cat_id'        => 18//默认疫苗提醒
////            );
////            $res_22 = $this->httpclient->post($url_22,$param_22);
////            $this->view->assign('eventData',$res_22['data']);
////            dump($res_22);
//            //***********
//        }
//        $this->user_info['bind_camera'] = $bind_camera;
////        $_SESSION = $this->user_info;
////        var_dump($_SESSION);
//        setcookie('user_info',$this->user_info,time()+86400);
//        $this->view->assign('bind_camera',$bind_camera);//宝宝有无绑定设备
//        $this->view->assign('entity_id',$entity_id);//宝宝id
//        $this->view->assign('week',ceil($this->days/7));//宝宝第几周
//
//
//        //*****广告轮播*****
//        if($bind_camera){
//            $url_21 = $this->item('app_home','home_carousel');
//        }else{
//            $url_21 = $this->item('app_home','home_advert');
//        }
//        $res_21 = $this->httpclient->get($url_21);
//        $this->view->assign('advertData',$res_21['data']);
//
//        //*****关注要点*****
////        $res_3 = $this->model('content_1_daily')->get_daily($this->days);
////        $this->view->assign("dailyData",$res_3);
//        //*****综合知识*****
////        $url_4 = $this->item('app_home','123');
//
//        //*****专家专栏*****
////        $url_5 = $this->item('app_home','123');
//
//        //*****用品清单(对应月龄随机文章)*****
////        $month = $this->month;
////        $topicModel = new Content_1_topicModel();
////        $res_6 = $topicModel->get_all("start<=$month and close>=$month",1,2);
////        $this->view->assign('hotTopic',$res_6['data']);
////        var_dump($res_6);
//
//        //*****看看推荐（最近一周随机推荐）*****
////        $url_7 = $this->item('app_home','get_hot_of_last_week');
////        $param_7 = $this->param;
////        $res_7 = $this->httpclient->post($url_7,$param_7);
////        $this->view->assign("diaryData",$res_7['_data']);
////        dump($res_7);
//
////        //*****试用推荐（正在进行试用商品随机推荐）*****
////        $url_8 = $this->item('tryout');
////        $param_8 = array(
////            "verify" => array(
////                "method" => 2
////            ),
////            "model" => "TryOutDetail",
////            "action" => "getList",
////            "condition" => array(
////                'field' => 'tryOutDetailId,activeName,tryOutDetailImg',
////                "page" => 1,
////                "page_size" => 10,
////                "view" => "getListTryOutDetail"
////            )
////        );
////        $res_8 = $this->httpclient->post($url_8, $param_8);
////        $tryCount = count($res_8['_data']['rows']);
////        $tryRand = rand(0, $tryCount);
//////        dump($res_8['_data']['rows'][$tryRand]);
////        $this->view->assign('tryoutData', $res_8['_data']['rows'][$tryRand]);
//
//        //*****商品推荐*****
//        $url_9 = $this->item('app_home', '123');
//
//        //
        $this->view->assign('url_bbc',$this->item('app_home','url_bbc'));
        $this->view->display('mobi/index/index');

        //TODO : 生成静态文件
        /*        $file_path = sprintf("./cache/html/home/%s_%s.html",$entity_id,$now_time+600);//1小时超时
                file_put_contents($file_path,ob_get_clean(), LOCK_EX);*/
    }

    
     public function getEntityIdAction () {
        $entity_id = $this->user_info['entity_id'];
        $ret = array('code'=>1,'info'=>'success','data'=>$entity_id);
        $this->ajaxReturn($ret);
    }
    
    public function getEntityDataAction() {  
        $entity = $this->user_info['entity'];        
        $ret = array('code'=>1,'info'=>'success','data'=>$entity);       
        $this->ajaxReturn($ret);
    }
    
    public function getbindCameraAction() {
        $bind_camera = $this->getbindCamera();
        $ret = array('code'=>1,'info'=>'success','data'=>$bind_camera);
        $this->ajaxReturn($ret);
    }
    
    public function getbindCamera() {        
        $bind_camera =  $this->user_info['bind_camera'];//宝宝有无绑定设备
        empty($bind_camera) and $bind_camera = 0; //是否绑定看宝器（摄像头）
        
        $entity_id = $this->user_info['entity_id'];
        if($entity_id){
            //*****设备信息(是否绑定看宝器)*****
            $url_1 = $this->item('app_home','get_device_list_by_entity');
            $param_1 = $this->param;
            $param_1['entity_ids'] = $entity_id;
            $res_1 = $this->httpclient->post($url_1,$param_1);
//            dump($res_1);
            if($res_1['status']['code']==200){
                foreach($res_1['entity_devices'][$entity_id] as $v){
//                    echo $v['device_type'];
                    if($v['device_type']==187){
                        $bind_camera = 1;//存在看宝器
                        break;
                    }
                }
            }
        }
        
        return $bind_camera;
    }
    
    public function getWeekAction() {
        $week = ceil($this->days/7);//宝宝第几周
        $week = $week ? $week: 1;
        $ret = array('code'=>1,'info'=>'success','data'=>$week);
        $this->ajaxReturn($ret);
    }
    
    public function getadvertDataAction()
    {
        $bind_camera = $this->getbindCamera();
        if($bind_camera){
            $url_21 = $this->item('app_home','home_carousel');
        }else{
            $url_21 = $this->item('app_home','home_advert');
        }
        $res_21 = $this->httpclient->get($url_21);
        
        $advertData = $res_21['data'];
        $ret = array('code'=>1,'info'=>'success','data'=>$advertData);
        $this->ajaxReturn($ret);
    }
    
    
    
    public function getURLBBCAction(){        
        $url_bbc = $this->item('app_home','url_bbc');
        $ret = array('code'=>1,'info'=>'success','data'=>$url_bbc);
        $this->ajaxReturn($ret);
    }
    
    
    
    
    
    /**
     * 异步重要提醒
     */
    public function eventAction(){
        //*****重要事情提醒*****
        $url_22 = $this->item('app_home','get_two_event');
        $param_22 = array(
            'uid'           => $this->user_info['uid'],
            'token'         => $this->user_info['token'],
            'entity_id'    => $this->user_info['entity_id'],
            'cat_id'        => 18//默认疫苗提醒
        );
        $res_22 = $this->httpclient->post($url_22,$param_22);
        $time = time();
        if($res_22['status']['code']==200){
            foreach($res_22['data'] as $k=>$v){
                $v['time'] = date('Y 年 m 月 d 日 H : i',$v['time']);
                $data[] = $v;
            }
            $ret = array('code'=>1,'info'=>'success','data'=>$data);
        }else{
            $ret = array('code'=>0,'info'=>'failure');
        }
//        $this->view->assign('eventData',$res_22['data']);
        $this->ajaxReturn($ret);
    }

    /**
     * 异步关注要点
     */
    public function dailyAction(){
        $res_3 = $this->model('content_1_daily')->get_daily($this->days,"content");
//        var_dump($res_3);
        if($res_3){
            if(isset($res_3['content'])){
                $res_3['content'] = (html_entity_decode($res_3['content']));     
            }
            $ret = array('code'=>1,'info'=>'success','data'=>$res_3);
        }else{
            $ret = array('code'=>0,'info'=>'failure');
        }
        $this->ajaxReturn($ret);
    }

    /**
     * 异步用品清单
     */
    public function supplyAction(){
        //*****用品清单(对应月龄随机文章)*****
        $month = $this->month;
        $topicModel = new Content_1_topicModel();
        $res_6 = $topicModel->get_all("start<=$month and close>=$month", 1, 1);
//        var_dump($res_6);
        if($res_6['data']){
            $ret = array('code'=>1,'info'=>'success','data'=>$res_6['data']);
        }else{
            $ret = array('code'=>0,'info'=>'failure');
        }
        $this->ajaxReturn($ret);
    }

    /**
     * 异步看看推荐
     */
    public function diaryAction(){
        $url_7 = $this->item('app_home','get_hot_of_last_week');
        $param_7 = $this->param;
        $res_7 = $this->httpclient->post($url_7,$param_7);
        if($res_7['_status']['code']==200){
            $ret = array('code'=>1,'info'=>'success','data'=>$res_7['_data']);
        }else{
            $ret = array('code'=>0,'info'=>'failure');
        }
        $this->ajaxReturn($ret);
    }

    /**
     * 异步试用推荐
     */
    public function tryoutAction(){
        //*****试用推荐（正在进行试用商品随机推荐）*****
        $url_8 = $this->item('tryout');
        $param_8 = array(
            "verify" => array(
                "method" => 2
            ),
            "model" => "TryOutDetail",
            "action" => "getList",
            "condition" => array(
                'field' => 'tryOutDetailId,activeName,tryOutDetailImg',
                "page" => 1,
                "page_size" => 10,
                "view" => "getListTryOutDetail"
            )
        );
        $res_8 = $this->httpclient->post($url_8, $param_8);
//        $tryCount = count($res_8['_data']['rows']);
//        $tryRand = rand(0, $tryCount);
//        dump($res_8['_data']['rows'][$tryRand]);
//        $this->view->assign('tryoutData', $res_8['_data']['rows'][$tryRand]);
        if($res_8['_status']['_code']==200){
            $ret = array('code'=>1,'info'=>'success','data'=>$res_8['_data']['rows']);
        }else{
            $ret = array('code'=>0,'info'=>'failure');
        }
        $this->ajaxReturn($ret);
    }

    /**
     * 异步异常现象
     */
    public function anomaliesAction()
    {
        $uid = $this->user_info['uid'];
        $token = $this->user_info['token'];
        $entity_id = $this->user_info['entity_id'];
        //获取设备实时数据
        $url_3 = $this->item('app_home', 'get_all_real_time_data');
        $param_3 = array(
            'uid' => $uid,
            'token' => $token,
            'entity_id' => $entity_id
        );
        $res_3 = $this->httpclient->post($url_3, $param_3);
        $sensors = array(
            '-', '温度', '湿度', '空气质量', '体重', '身高', '体表体温', '心跳'
        );
//        var_dump($url_3,$param_3,$res_3);

        if ($res_3['status']['code'] == 200) {
            foreach ($res_3['data'] as $v) {
                $v['level'] && in_array($v['type'], array(4, 5, 6, 7)) and $abnormalData[] = sprintf("当前宝宝%s不在正常范围内，请及时查看", $sensors[$v['type']]);
                $v['level'] && in_array($v['type'], array(1, 2)) and $abnormalData[] = sprintf("当前室内%s不在正常范围内，请及时调整", $sensors[$v['type']]);
                $v['level'] && in_array($v['type'], array(3)) and $abnormalData[] = sprintf("当前室内%s不佳，请及时开窗透气", $sensors[$v['type']]);
            }
            $ret = array('code' => 1, 'info' => 'get success','data'=>$abnormalData);
        } else {
            $ret = array('code' => 0, 'info' => 'get failure');
        }
        $this->ajaxReturn($ret);
    }


}