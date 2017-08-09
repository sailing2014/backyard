<?php

include_once TRY_PATH.'/TryController.php';
class ActivityController extends TryController {
    public $statusList;
    public function __construct(){
        parent::__construct();
        $this->statusList = array(
            '预告中','申请中','筛选中','回收中','已结束',
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
        $productId = $this->get('productId');

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
        $param = $param1 = $this->param;
        $param['model']="TryOutDetail";
        $param['action']="getList";
        $param['condition']['page'] = $page;
        $param['condition']['page_size'] = $size;
        $param['condition']['view'] = "getListTryOutDetailAll";
        $productId and $param['condition']['productId']=$productId;
        //$status and $param[] = array('status'=>$status);
        $result = $this->httpclient->post($url,$param);
//        dump(($param));
//        dump($result);
        if($result['_data']['total_rows']){
            //批量获取产品
            foreach($result['_data']['rows'] as $v){
                $doc_ids[]=$v['productId'];
            }
            $param1['model']="Product";
            $param1['action']="getMultiple";
            $param1['doc_id'] = join(',',$doc_ids);
            $param1['condition']['field'] = "productName,smallImg";
            $product = $this->httpclient->post($url,$param1);
//            dump($param1);
//            dump($product);
            $this->view->assign('product',$product['_data']);
            //
            $pagelist 	= $this->instance('pagelist');	//加载分页类
            $pagelist->loadconfig();
            $result['pagelist']	= $pagelist->total($result['_data']['total_rows'])->url(url('try/activity/index', $map))->num($size)->page($page)->output();
        }else{

        }
//        dump($result);
        $this->view->assign('map',$map);
        $this->view->assign('statusList',$this->statusList);
        $this->view->assign('data',$result);
        $this->view->display('try/activity/all');
    }

    /**
     * 修改
     */
    public function oneAction(){
        $url = $this->item('tryout');
        $param = $param1 = $this->param;
        $param['model'] = "TryOutDetail";
        $id = $this->get('id');

        if($this->is_post()){
            $_POST['startTime'] = strtotime($_POST['startTime']);//活动申请开放时间
            $_POST['applyEndTime'] = strtotime($_POST['applyEndTime']);//活动申请结束时间
            $_POST['releaseTime'] = strtotime($_POST['releaseTime']);//报告审核结束时间
            $_POST['deadTime'] = strtotime($_POST['deadTime']);//活动回收结束时间

            $param['data'] = $_POST;

            if(!empty($id)){
                $param['doc_id'] = $id;
                $param['action'] = 'update';
            }else{
                $param['action'] = 'add';
                $param['data']['productId'] = $this->get('productId');
            }
            $result = $this->httpclient->post($url,$param);
//            var_dump(json_encode($param));
//            dump($result);
            if($result['_status']['_code']==200){
                $response = array('code'=>1,'info'=>'保存成功');
            }else{
                $response = array('code'=>0,'info'=>'保存失败');
            }
            $this->ajaxReturn($response);
        }else{
            if(!empty($id)){
                $param['model']="Activity";
                $param['action']="getOne";
                $param['doc_id']=$id;
                $result = $this->httpclient->post($url,$param);
//                var_dump($param);
//                var_dump($result);
                if($result['_status']['_code']==200){
                    $param1['doc_id'] = $result['_data']['productId'];
                    //活动状态
                    $timeList = array(
                        0,
                        $result['_data']['startTime'],
                        $result['_data']['applyEndTime'],
                        $result['_data']['releaseTime'],
                        $result['_data']['deadTime']
                    );
//                    $time = time();
//                    foreach($timeList as $k=>$v){
//                        if($time>$v && $time<$timeList[$k+1]){
//                            $result['_data']['status'] = $this->statusList[$k];
//                            break;
//                        }
//                    }
                }
            }else{
                $param1['doc_id'] = $this->get('productId');
                $result['_data']['startTime'] = time();//默认活动申请开始时间
                $result['_data']['applyEndTime'] = time();//默认活动申请结束时间
                $result['_data']['releaseTime'] = time();//默认报告审核结束时间
                $result['_data']['deadTime'] = time();//默认报告回收结束时间
                $result['_data']['createTime'] = time();
            }

            //相关商品
            $param1['model'] = "Product";
            $param1['action'] = "getOne";
            $result1 = $this->httpclient->post($url,$param1);
            $result['_data']['product'] = $result1['_data'];
//            dump($param);
//            dump($result);


            $this->view->assign('data',$result['_data']);
            $this->view->assign('statusList',$this->statusList);
            $this->view->assign('statusList1',array( "下架","上架"));
            $this->view->display('try/activity/one');
        }
    }

    /**
     * 批量删除
     */
    public function delAction(){
        $ids = $this->get('ids');
        $url = $this->item('tryout');
        $param = $this->param;
        $param['model'] = "Activity";
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
        $param['model'] = "Activity";
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
}