<?php
include_once CONTROLLER_DIR.'Cloud.php';
class Device_ruleController extends Cloud{
    protected $devices;
    public function __construct() {
		parent::__construct();
        $this->devices= array(187=>'底座',188=>'称',191=>'绑带',172=>'空气净化器');
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
        error_log("[".date('Y-m-d H:i:s')."] start enter Device_rule all Action ...\n",3,"/home/bbc_helper/log/sys.log");
		$url = $this->item('device_rule','get_all');
        $result = $this->httpclient->get($url, $this->param);
        error_log("[".date('Y-m-d H:i:s')."] start get_all ".$url." ...\n",3,"/home/bbc_helper/log/sys.log");
        //dump($result);
        if (isset($result['status']) && $result['status']['code'] == 200) {
            error_log("[".date('Y-m-d H:i:s')."] success end get_all ".$url." with code 200 ...\n",3,"/home/bbc_helper/log/sys.log");
            $sensors = array();
            foreach ($result['data'] as &$v) {
                $v['sensors'] 		= json_decode($v['sensors'], true);
                $v['config_value'] 	= json_decode( $v['config_value'], true);
                //cache
                $devices[$v['device_type']] = array(
                    'type'      =>  $v['device_type'],
                    'name'      =>  $this->devices[$v['device_type']],
                    'sensors'   => $v['sensors'],
                );
                $sensors +=  $v['sensors'];
            }
            unset($v);
            //缓存设备类型以及相关传感器
            $this->cache->set('devices',$devices);
            $sensors[9] = '翻身角度';
            $sensors[23]= '绑带电量';
            //dump($sensors);
            $this->cache->set('sensors',$sensors);
        } else {
            error_log("[".date('Y-m-d H:i:s')."] failed ending get_all ".$url." ...\n",3,"/home/bbc_helper/log/sys.log");
            $result['message'] = "操作失败";
        }
        //dump($result['data']);
		$this->view->assign('data',$result['data']);
		$this->view->assign('devices',$devices);
        //dump($devices);
		$this->view->display('admin/device_rule/all');
	}

	/**
	 * 
	 */
	public function oneAction(){
		$id = $this->get('id');
        $param = $param1 = $this->param;
        if($this->is_post()){

            if($id){//编辑
                $url = $this->item('device_rule','set_one');
                $param1['id'] = $id;
            }else{//保存
                $url = $this->item('device_rule','add_one');
            }
            //dump($_POST);
            foreach ($_POST as $k => $v) {
                foreach ($v as $kk => $vv) {
                    $config_value[$kk][$k] = (int)$vv;
                }
            }
            //dump($config_value);
            $config['config_value'] = $config_value;
            $config = json_encode($config);
            $param1['data'] = $config;
            //var_dump($config);
            $result = $this->httpclient->post($url, $param1);
            if (isset($result) && $result['status']['code'] == 200) {
                $return = array('code'=>1,'info'=>'操作成功');
            } else {
                $return = array('code'=>0,'info'=>'操作失败');
            }
            $this->ajaxReturn($return);

        }else{

            if($id){//查看
                $url = $this->item('device_rule','get_one');
                $param['id'] = $id;
                $result = $this->httpclient->get($url, $param);
                if( $result['status']['code']==200){
                    foreach($result['data'] as $k =>&$v){
                        $k == 'sensors' and $v = json_decode($v,true);
                        $k == 'config_value' and $v = json_decode($v,true);
                    }
                    unset($v);
                }else{
                    $this->adminMsg(lang('failure'));
                }
                $this->view->assign('data',$result['data']);
            }else{//添加

            }
            $this->view->assign('devices',$this->cache->get('devices'));
            $this->view->display('admin/device_rule/one');
        }

	}

	/**
	 * 
	 */
	public function delAction(){
		$url = $this->item('device_rule','del_one');
        $id = $this->get('id');
        $param = $this->param;
        $param['id'] = $id;
        $result = $this->httpclient->get($url, $param);
        if(isset($result['status']) && $result['status']['code']==200){
           $return = array('code'=>1,'info'=>'删除成功');
        }else{
           $return = array('code'=>0,'info'=>'删除失败');
        }
        $this->ajaxReturn($return);
	}

        
}
