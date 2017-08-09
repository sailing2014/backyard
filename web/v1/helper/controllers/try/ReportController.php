<?php

include_once TRY_PATH.'/TryController.php';
class ReportController extends TryController {
    public $statusList;
    protected $if;
    public function __construct(){
        parent::__construct();
        $this->statusList = array(2=>'审核中','成功','失败');
        $this->if = array('否','是');
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
        $param = $param1 = $this->param;
        $param['model']="Report";
        $param['action']="getList";
        $param['condition']['page'] = $page;
        $param['condition']['page_size'] = $size;
        $status and $param['condition']['status']=$status;

        $result = $this->httpclient->post($url,$param);
//        dump(json_encode($param));
//        dump($result);
        if($result['_data']['total_rows']){
            foreach($result['_data']['rows'] as $v){
                $doc_ids[]=$v['productId'];
                $v['uid'] and $uids[$v['uid']]=$v['uid'];
            }
            //批量获取用户昵称
            $url2 = $this->item('user','get_one_by_uids');
            $param2 = $this->param['verify'];
            $param2['uids'] = join(',',$uids);
            $users = $this->httpclient->post($url2,$param2);
//            dump($users);
            foreach($result['_data']['rows'] as $k=>&$v){
                $v['nickname'] = $users['users']['user::'.$v['uid']]['nickname'];
            }
            unset($v);

            //批量获取产品
            $param1['model']="Product";
            $param1['action']="getMultiple";
            $param1['doc_id'] = join(',',$doc_ids);
            $param1['condition']['field'] = "productName,smallImg";
            $product = $this->httpclient->post($url,$param1);
            $this->view->assign('product',$product['_data']);
            //
            $pagelist 	= $this->instance('pagelist');	//加载分页类
            $pagelist->loadconfig();
            $result['pagelist']	= $pagelist->total($result['_data']['total_rows'])->url(url('try/report/index', $map))->num($size)->page($page)->output();
        }else{

        }
//        dump($result);
        $this->view->assign('map',$map);
        $this->view->assign('statusList',$this->statusList);
        $this->view->assign('if',$this->if);
        $this->view->assign('data',$result);
        $this->view->display('try/report/all');
    }

    /**
     * 修改
     */
    public function oneAction(){
        $url = $this->item('tryout');
        $param = $param1 = $this->param;
        $param['model'] = "Report";
        $id = $this->get('id');

        if($this->is_post()){
            $param['data'] = $_POST;
            $param['data']['advantage'] =  preg_replace("/[\r\n]+/", "<br>", $param['data']['advantage']);
            $param['data']['advice'] =  preg_replace("/[\r\n]+/", "<br>", $param['data']['advice']);
            $param['data']['productDes'] = preg_replace("/[\r\n]+/", "<br>", $param['data']['productDes']);
            if(!empty($id)){
                $param['doc_id'] = $id;
                $param['action'] = 'update';
            }else{
                $param['action'] = 'add';
            }
            $result = $this->httpclient->post($url,$param);
            if($result['_status']['_code']==200){
                $response = array('code'=>1,'info'=>'保存成功');
            }else{
                $response = array('code'=>0,'info'=>'保存失败');
            }
            $this->ajaxReturn($response);
        }else{
            if(!empty($id)){
                $param['model'] = "Report";
                $param['action'] = "getOne";
                $param['doc_id'] = $id;
                $result = $this->httpclient->post($url,$param);
//                dump($param);
//                dump($result);
                if($result['_status']['_code']==200){
                    $result['_data']['advantage']= preg_replace("/<br[^>]*>/i", "\n", $result['_data']['advantage']);
                    $result['_data']['advice']= preg_replace("/<br[^>]*>/i", "\n", $result['_data']['advice']);
                    $result['_data']['productDes']= preg_replace("/<br[^>]*>/i", "\n", $result['_data']['productDes']);

                    //相关用户
                    $url2 = $this->item('user','get_one_by_uids');
                    $param2 = $this->param['verify'];
                    $param2['uids'] = $result['_data']['uid'];
                    $users = $this->httpclient->post($url2,$param2);

                    $result['_data']['nickname'] = $users['users']['user::'.$result['_data']['uid']]['nickname'];
//                    dump($users);
                    //相关商品
                    $param1['doc_id'] = $result['_data']['productId'];
                    $param1['model'] = "Product";
                    $param1['action'] = "getOne";
                    $result1 = $this->httpclient->post($url,$param1);
                    $result['_data']['product'] = $result1['_data'];
//                    dump($result);
                    $this->view->assign('data',$result['_data']);
                }
            }else{

            }
//            dump($result);
            $this->view->assign('statusList',$this->statusList);
            $this->view->assign('if',$this->if);
            $this->view->assign('scoreList',array(0,0.5,1,1.5,2,2.5,3,3.5,4,4.5,5));
            $this->view->display('try/report/one');
        }
    }

    /**
     * 批量删除
     */
    public function delAction(){
        $ids = $this->get('ids');
        $url = $this->item('tryout');
        $param = $this->param;
        $param['model'] = "Report";
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
        $param['model'] = "Report";
        $param['action'] = "updateMul";
        $param['doc_id'] = $ids;
        $param['data'] = array('status'=>$status);
        $result = $this->httpclient->post($url,$param);
//        var_dump($url,$param,$result);
        if($result['_status']['_code']==200){
            $response=array('code'=>1,'info'=>'设置成功');
        }else{
            $response=array('code'=>0,'info'=>'设置失败');
        }
        $this->ajaxReturn($response);
    }
}