<?php
include_once CONTROLLER_DIR.'Cloud.php';
class Device_infoController extends Cloud{
    public function __construct() {
		parent::__construct();
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
        $url = $this->item('device_info','get_all');
        $param = $this->param;
        $uid = $this->get('uid');
        $device_id = $this->get('device_id');
        $device_type = $this->get('device_type');
        $page= $this->get('page');

        $param['uid'] = $uid ? $uid : 0;
        $param['device_id'] = $device_id ? $device_id : 0;
        $param['device_type'] = $device_type ? $device_type : 0;
        $param['page'] = $page ? $page : 1;
        $param['page_size'] = 10;
        $result = $this->httpclient->post($url, $param);

        if (isset($result['status']) && $result['status']['code'] == 200) {
            //
            $pagelist 	= $this->instance('pagelist');	//加载分页类
            $pagelist->loadconfig();
            $map = array(
                'uid'=>$uid,
                'device_id'=>$device_id,
                'device_type'=>$device_type,
                'page'=>'{page}'
            );
            $result['pagelist']	= $pagelist -> total($result['count']) -> url(url('admin/device_info/index', $map)) -> num($param['page_size'])-> page($param['page'])-> output();

        } else {
            $result['message'] = "操作失败";
        }
        //dump($result);
        $this->view->assign('data',$result);
        $this->view->assign('map',$map);
        $this->view->assign('devices',$this->cache->get('devices'));
		$this->view->display('admin/device_info/all');
	}
	/**
	 * 
	 */
	public function oneAction(){
		$this->view->display('admin/device_info/one');
	}
	/**
	 * 
	 */
	public function delAction(){

	}

}