<?php
include_once CONTROLLER_DIR.'Cloud.php';
class AnalysisController extends Cloud{
	protected $genders;
	protected $devices;
	protected $sensors;

    public function __construct() {
		parent::__construct();
		$this->genders = array(3=>'不限',1=>'男',2=>'女');
		//$this->devices = array(187=>'底座', 188=>'称', 191=>'绑带');
		//$this->sensors = array(1=>'室温', '湿度', '空气', '体重', '身高', '体温', '心率',9=>'..',23=>'绑带电量');
		
	}

	public function indexAction(){
		$this->allAction();
	}

	/**
	 * 
	 */
	public function allAction(){
		$device_type = $this->get('device_type');
		$data_type = $this->get('data_type');
		$month = $this->get('month');
		$gender = $this->get('gender');
		$page = $this->get('page') ? $this->get('page') : 1;

		$map = array(
				'device_type'	=> $device_type,
				'month'		=> $month,
				'gender'		=> $gender,
				'page'			=> '{page}',
			);
        $data_type!=='' and $map['data_type'] = $data_type;
        //dump($map);

		$where[]='1=1';
		$device_type 	and $where[] 	= "device_type 	= $device_type";
		$data_type!==''	and $where[] 	= "data_type = $data_type";
		$month 			and $where[] 	= "month = $month";
		$gender 		and $where[] 	= "gender = $gender";

		//
		$this->param['where'] 	= json_encode(join(' and ',$where));
		$this->param['page'] 	= $page;
		$this->param['size']	= 30;
		
		//
		$url 	= $this->item('analysis','get_all');
		$result = $this->httpclient->get($url,$this->param);
		
		if (isset($result['status']) && $result['status']['code'] == 200) {
			
			$pagelist 	= $this->instance('pagelist');	//加载分页类
			$pagelist->loadconfig();
			$result['data']['pagelist']	= $pagelist	->total($result['data']['count'])
													->url(url('admin/analysis/index', $map))
													->num($result['data']['size'])
													->page($result['data']['page'])
													->output();

		}else{

		}
		//dump($result);
		//unset($map['page']);
		$this->view->assign('map',$map);

		$this->view->assign('genders',$this->genders);
		$this->view->assign('devices',$this->cache->get('devices'));
		$this->view->assign('sensors',$this->cache->get('sensors'));

		$this->view->assign('data',$result['data']);
		$this->view->display('admin/analysis/all');
	}

	/**
	 * 
	 */
	public function oneAction(){
		$id = $this->get('id');
		$param = $param1 = $param2 = $this->param;
		if($this->is_post()){
			if($id){//保存
				$url = $this->item('analysis','set_one');
				$this->param['id'] = $id;
			}else{//添加
				$url = $this->item('analysis','add_one');
			}
			unset($_POST['submit']);
            $this->param['data'] = json_encode( $_POST );
            $result = $this->httpclient->post($url, $this->param);
            if (isset($result) && $result['status']['code'] == 200) {
                $return = array('code'=>1,'info'=>'操作成功');
            } else {
                $return = array('code'=>0,'info'=>'操作失败');
            }
            $this->ajaxReturn($return);
		}else{	

			if($id){//查看
				$url = $this->item('analysis','get_one');
				$param['id'] = $id;
				$result = $this->httpclient->get($url,$param);
				if($result['status']['code']==200){

				}else{
					$result['data']['info'] = '拉取数据失败';
				}
				$this->view->assign('data',$result['data']);
				
			}
			$this->view->assign('genders',$this->genders);
            $this->view->assign('devices',$this->cache->get('devices'));
            $this->view->assign('sensors',$this->cache->get('sensors'));
			$this->view->display('admin/analysis/one');
		}
	}

	/**
	 * 
	 */
	public function  delAction(){
        $url = $this->item('analysis','del_one');
        $id = urldecode($this->get('id'));
        $this->param['id'] = $id;
        $result = $this->httpclient->get($url, $this->param);
        if(isset($result['status']) && $result['status']['code']==200){
            $return = array('code'=>1,'info'=>'删除成功');
        }else{
            $return = array('code'=>0,'info'=>'删除失败');
        }
        $this->ajaxReturn($return);
	}

}