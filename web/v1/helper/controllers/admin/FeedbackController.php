<?php
include_once CONTROLLER_DIR.'Cloud.php';
class FeedbackController extends Cloud{
    protected $status;
    public function __construct() {
		parent::__construct();
        $this->status = array('选择状态','待反馈','处理中','处理完');
        //接口认证方式不同故另外配置
        $time = time();
        $this->param = array(
            'time'       => $time,
            'api_token' => sha1($time."abcdefg")
        );
	}
	/**
	 * 
	 */
	public function indexAction(){
		$this->allAction();
	}

	/**
	 * 
	 */
	public function allAction(){

		$param = $this->param;

		$user_id = $this->get('user_id');
        $user_id and $where[] = "uid = ".$user_id;

        $feedback_id = $this->get('feedback_id');
        $feedback_id and $where[] = "fid = ".$feedback_id;

        $status = $this->get('status');
        $status and $where[] = "status = ".$status;

		$page = $this->get('page');
        $page = $page ? $page : 1;
        $size = $this->get('size');
        $size = $size ? $size :10;

		$param['page'] = $page;
        $param['size'] = $size;
        $param['field'] = "fid,uid,title,content,time,status";
		$where and $where = join(' and ',$where);
		$where and $param['where'] = $where;

		$map = array(
				'user_id'	     => $user_id,
				'feedback_id'	 => $feedback_id,
				'status'		 => $status,
				'page'		     => '{page}',
                'size'          => $size,
			);
		$url = $this->item('feedback','get_all');
		$result = $this->httpclient->post($url,$param);
		if($result['_status']['_code']==200){
            //
            $pagelist 	= $this->instance('pagelist');	//加载分页类
			$pagelist->loadconfig();
			$result['pagelist']	= $pagelist->total($result['_count'])->url(url('admin/feedback/index', $map))->num($size)->page($page)->output();

		}else{

		}
		$this->view->assign('map',$map);
		$this->view->assign('status',$this->status);
		$this->view->assign('data',$result);
		$this->view->display('admin/feedback/all');
	}

	/**
	 * 
	 */
	public function oneAction(){
		$id = $this->get('id');
        $param = $this->param;
        if($this->is_post()){
            if($id){//编辑

            }else{//保存

            }
        }else{
            if($id){//查看
                $param['id'] = $id;
                $url = $this->item('feedback','get_one');
                $result = $this->httpclient->post($url, $param);
                //echo  mb_detect_encoding($content, array('ASCII','UTF-8','GB2312','GBK','BIG5'));
                if($result['_status']['_code']==200){

                    $this->view->assign('data',$result['_data']);

                }else{
                    $this->adminMsg(lang('failure'));
                }
            }else{//添加

            }
            $this->view->assign('status',$this->status);
            $this->view->display('admin/feedback/one');
        }
    }

	/**
	 * 
	 */
	public function delAction(){
		$url = $this->item('feedback','del_one');
        $id = $this->get('id');
        $param = $this->param;
        $param['id'] = $id;
        $result = $this->httpclient->post($url, $param);
        if($result['_status']['_code']==200){
           $return = array('code'=>1,'info'=>'删除成功');
        }else{
           $return = array('code'=>0,'info'=>'删除失败');
        }
        $this->ajaxReturn($return);
	}

}