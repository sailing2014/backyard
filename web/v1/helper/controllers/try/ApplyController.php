<?php

include_once TRY_PATH.'/TryController.php';
class ApplyController extends TryController {
    public $statusList;
    public $expressList;
    public function __construct(){
        parent::__construct();
        $this->statusList = array(
            1=>"申请中",
            "成功",
            "失败",
            "已发货",
            "已提交"
        );
        $this->expressList= array(
            'ems'=>'EMS',
            'shentong'=>'申通',
            'zhongtong'=>'中通',
            'yunda'=>'韵达',
            'zaijisong'=>'宅急送',
            'youzhengguonei'=>'邮政国内',
            'shunfeng'=>'顺丰',
            'yuantong'=>'圆通',
            'huitong'=>'汇通',
            'tiantian'=>'天天',
            'quanfengkuaidi'=>'全峰',
            'rufengda'=>'如风达'
        );

    }

    /**
     *
     */
    public function indexAction(){
        $this->allAction();
    }

    public function allAction(){
        $status = $this->get('status');

        $page = $this->get('page');
        $page = $page ? $page : 1;
        $size = $this->get('size');
        $size = $size ? $size :10;

        $map = array(
            'status'		 => $status,
            'page'		     => '{page}',
            'size'          => $size,
        );
        $url = $this->item('tryout');
        $param = $param1 = $param2 = $this->param;
        $param['model']="TryOut";
        $param['action']="getList";
        $param['condition']['page'] = $page;
        $param['condition']['page_size'] = $size;
        $status and $param['condition']['status']=$status;

        $result = $this->httpclient->post($url,$param);
//        dump(json_encode($param));
//        dump($result);
        if($result['_data']['total_rows']){
            //批量获取商品
            foreach($result['_data']['rows'] as $v){
                $doc_ids_1[]=$v['productId'];
            }
            $param1['model']="Product";
            $param1['action']="getMultiple";
            $param1['doc_id'] = join(',',$doc_ids_1);
            $param1['condition']['field'] = "productName";
            $product = $this->httpclient->post($url,$param1);
//            dump($param1);
//            dump($product);
            $this->view->assign('product',$product['_data']);
            //
            //批量获取活动
            foreach($result['_data']['rows'] as $v){
                $doc_ids_2[]=$v['tryOutDetailId'];
            }
            $param2['model']="TryOutDetail";
            $param2['action']="getMultiple";
            $param2['doc_id'] = join(',',$doc_ids_2);
            $param2['condition']['field'] = "activeName";
            $activity = $this->httpclient->post($url,$param2);
//            dump($param2);
//            dump($activity);
            $this->view->assign('activity',$activity['_data']);
            //
            //批量获取活动
            foreach($result['_data']['rows'] as $v){
                $doc_ids_2[]=$v['tryOutDetailId'];
            }
            $param2['model']="TryOutDetail";
            $param2['action']="getMultiple";
            $param2['doc_id'] = join(',',$doc_ids_2);
            $param2['condition']['field'] = "activeName";
            $activity = $this->httpclient->post($url,$param2);
//            dump($param2);
//            dump($activity);
            $this->view->assign('activity',$activity['_data']);
            //
            //
            $pagelist 	= $this->instance('pagelist');	//加载分页类
            $pagelist->loadconfig();
            $result['pagelist']	= $pagelist->total($result['_data']['total_rows'])->url(url('try/apply/index', $map))->num($size)->page($page)->output();
        }else{

        }
//        dump($result);
        $this->view->assign('map',$map);
        $this->view->assign('statusList',$this->statusList);
        $this->view->assign('expressList',$this->expressList);
        $this->view->assign('data',$result);
        $this->view->display('try/apply/all');
    }

    /**
     * 修改
     */
    public function oneAction(){
        $url = $this->item('tryout');
        $param = $this->param;
        $param['model'] = "TryOut";
        $id = $this->get('id');

        if($this->is_post()){
            $param['data'] = $_POST;
            if(!empty($id)){
                $param['doc_id'] = $id;
                $param['action'] = 'update';
            }else{
                $param['action'] = 'add';
            }
            $result = $this->httpclient->post($url,$param);
//            var_dump($result);
            if($result['_status']['_code']==200){
                $response = array('code'=>1,'info'=>'保存成功');
            }else{
                $response = array('code'=>0,'info'=>'保存失败');
            }
            $this->ajaxReturn($response);
        }else{
            if(!empty($id)){
                $param['model']="TryOut";
                $param['action']="getOne";
                $param['doc_id']=$id;
                $result = $this->httpclient->post($url,$param);
//                var_dump($url,$param,$result);
                if($result['_status']['_code']==200){
                    //相关商品
                    $param['model']="Product";
                    $param['action']="getOne";
                    $param['doc_id']=$result['_data']['productId'];
                    $result2 = $this->httpclient->post($url,$param);
                    $result['_data']['product'] = $result2['_data'];
//                    dump($result);
                    $this->view->assign('data',$result['_data']);
                }
            }else{

            }
            $this->view->assign('statusList',$this->statusList);
            $this->view->assign('expressList',$this->expressList);
            $this->view->display('try/apply/one');
        }
    }

    /**
     * 批量删除
     */
    public function delAction(){
        $ids = $this->get('ids');
        $url = $this->item('tryout');
        $param = $this->param;
        $param['model'] = "TryOut";
        $param['action'] = "delete";
        $param['doc_id'] = $ids;
        $result = $this->httpclient->post($url,$param);
        if($result['_status']['_code']==200){
            $response=array('code'=>1,'info'=>'删除成功');
        }else{
            $response=array('code'=>0,'info'=>'删除失败');
        }
        $this->ajaxReturn($response);
    }

    /**
     * 批量设置
     */
    public function setAction(){
        $ids = $this->get('ids');
        $status = (int)$this->get('status');
        $url = $this->item('tryout');
        $param = $this->param;
        $param['model'] = "TryOut";
        $param['action'] = "updateMul";
        $param['doc_id'] = $ids;
        $param['data'] = array('status'=>$status);
        $result = $this->httpclient->post($url,$param);
//        var_dump($param);
//        var_dump($result);
        if($result['_status']['_code']==200){
            $response=array('code'=>1,'info'=>'设置成功');
        }else{
            $response=array('code'=>0,'info'=>'设置失败');
        }
        $this->ajaxReturn($response);
    }

    /**
     * 获取快递信息
     */
    public function getPostInfoAction(){
        $type = $this->get('type');
        $postid = $this->get('postid');
        $info = file_get_contents("http://www.kuaidi100.com/query?type=$type&postid=$postid");
        $info = json_decode($info,true);
        $this->ajaxReturn($info);
    }
}