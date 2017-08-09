<?php

include_once TRY_PATH.'/TryController.php';
class ExpressController extends TryController {
    public $statusList;
    public function __construct(){
        parent::__construct();
        $this->statusList = array(
            "屏蔽",
            "展示"
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
        $param = $this->param;
        $param['model']="Express";
        $param['action']="getList";
        $param['condition']['page'] = $page;
        $param['condition']['page_size'] = $size;
        $status and $param['condition']['status']=$status;

        $result = $this->httpclient->post($url,$param);
//        dump($param);
//        dump($result);
        if($result['_data']['total_rows']){
            //批量获取报告
            foreach($result['_data']['rows'] as $v){
                $doc_ids[]=$v['doc_id'];
            }
            $param1['model']="Report";
            $param1['action']="getMultiple";
            $param['doc_id'] = join(',',$doc_ids);
            $param1['condition']['field'] = "topic";
            $report = $this->httpclient->post($url,$param1);
            $this->view->assign('report',$report['_data']);
            //
            $pagelist 	= $this->instance('pagelist');	//加载分页类
            $pagelist->loadconfig();
            $result['pagelist']	= $pagelist->total($result['_data']['total_rows'])->url(url('try/comment/index', $map))->num($size)->page($page)->output();
        }else{

        }
//        dump($result);
        $this->view->assign('map',$map);
        $this->view->assign('status',$this->status);
        $this->view->assign('data',$result);
        $this->view->display('try/express/all');
    }

    /**
     * 修改
     */
    public function oneAction(){
        $url = $this->item('tryout');
        $param = $this->param;
        $param['model'] = "Comment";
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
            if($result['_status']['_code']==200){
                $response = array('code'=>1,'info'=>'保存成功');
            }else{
                $response = array('code'=>0,'info'=>'保存失败');
            }
            $this->ajaxReturn($response);
        }else{
            if(!empty($id)){
                $param['model']="Express";
                $param['action']="getOne";
                $param['doc_id']=$id;
                $result = $this->httpclient->post($url,$param);
//                dump($param);
//                dump($result);
                if($result['_status']['_code']==200){

                }
            }else{
                $result['_data']['createTime'] = time();
            }
            $this->view->assign('data',$result['_data']);
            $this->view->assign('statusList',$this->statusList);
            $this->view->display('try/express/one');
        }
    }

    /**
     * 批量删除
     */
    public function delAction(){
        $ids = $this->get('ids');
        $url = $this->item('tryout');
        $param = $this->param;
        $param['model'] = "Express";
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
        $param['model'] = "Express";
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