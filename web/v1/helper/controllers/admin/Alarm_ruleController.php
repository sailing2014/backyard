<?php
include_once CONTROLLER_DIR.'Cloud.php';
class Alarm_ruleController extends Cloud{
    public function __construct() {
		parent::__construct();
	}

    /**
     *
     */
    public function indexAction()
    {
        $this->allAction();
    }

    /**
     *
     */
    public function allAction()
    {
        $url = $this->item('alarm_rule','get_all');
        $param = $this->param;
        $result = $this->httpclient->post($url,$param);
        if($result['status']['code']==200){
            //dump($result);
        }else{

        }
        //dump($this->cache->get('devices'));
        $this->view->assign('devices',$this->cache->get('devices'));
        //dump($this->cache->get('sensors'));
        $this->view->assign('sensors',$this->cache->get('sensors'));
        $this->view->assign('data',$result['config']);
        $this->view->display('admin/alarm_rule/all');
    }

    /**
     *
     */
    public function oneAction()
    {
        $id = $this->get('id');
        $param = $this->param;
        if($this->is_post()){//编辑
            if($id){
                $url =$this->item('alarm_rule','set_one');
                $param['id']= $id;
//                $param['device_type'] = $this->post('device_type');
//                $param['alarm_type']= $this->post('alarm_type');
                $param['cycle'] = $this->post('cycle');
                $param['count'] = $this->post('count');
                $param['alarm_interval'] = $this->post('alarm_interval');
                $param['alarm_info_id'] = $this->post('alarm_info_id');
                //dump($param);
                $result = $this->httpclient->post($url,$param);
                //dump($result);
                if ($result['status']['code'] == 200) {
                    $return = array('code'=>1,'info'=>'操作成功');
                } else {
                    $return = array('code'=>0,'info'=>'操作失败');
                }
                $this->ajaxReturn($return);
            }else{//保存

            }
        }else{
            if($id){//查看
                $url = $this->item('alarm_rule','get_one');
                $param['id'] = $id;
                $result = $this->httpclient->post($url,$param);
                if($result['status']['code']==200){
                    //dump($result);
                }else{

                }
            }else{//添加

            }
            $this->view->assign('data', $result['config'][0]);
            $this->view->display('admin/alarm_rule/one');
        }
    }
}