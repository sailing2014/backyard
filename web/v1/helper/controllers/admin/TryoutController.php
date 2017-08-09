<?php
include_once CONTROLLER_DIR.'Cloud.php';
class TryoutController extends Cloud{
    protected $status;
    public function __construct() {
		parent::__construct();
        $this->status = array('申请','审核');
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

        $status = $this->get('status');
        $status and $where[] = "status = ".$status;

		$page = $this->get('page');
        $page = $page ? $page : 1;
        $size = $this->get('size');
        $size = $size ? $size :10;

		$map = array(
				'status'		 => $status,
				'page'		     => '{page}',
                'size'          => $size,
			);
        $result = $this->model('tryout')->get_all($page,$size);
		if($result['count']){
            //
            $pagelist 	= $this->instance('pagelist');	//加载分页类
			$pagelist->loadconfig();
			$result['pagelist']	= $pagelist->total($result['count'])->url(url('admin/tryout/index', $map))->num($size)->page($page)->output();
		}else{

		}
		$this->view->assign('map',$map);
		$this->view->assign('status',$this->status);
		$this->view->assign('data',$result);
		$this->view->display('admin/tryout/all');
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

                $result = $this->httpclient->post($url, $param);
                //dump($result);
                if($result['_status']['_code']==200){

                    $this->view->assign('data',$result['_data']);

                }else{
                    $this->adminMsg(lang('failure'));
                }
            }else{//添加

            }
            $this->view->assign('status',$this->status);
            $this->view->display('admin/tryout/one');
        }
    }

	/**
	 * 
	 */
	public function delAction(){
        $id = $this->get('id');
        $result = $this->model('tryout')->del($id);
        if($result){
           $return = array('code'=>1,'info'=>'删除成功');
        }else{
           $return = array('code'=>0,'info'=>'删除失败');
        }
        $this->ajaxReturn($return);
	}

}