<?php
include_once CONTROLLER_DIR.'Cloud.php';
class Phone_bindController extends Cloud{
    protected $binding_way;
    public function __construct() {
		parent::__construct();
        $this->binding_way = array('绑定方式','蓝牙');
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
        $url = $this->item('phone_bind','get_all');
        $param = $param1 = $this->param;
        $name = $this->get('name');
        $type = $this->get('type');
        $page = $this->get('page');

        $name and $param['name'] = $name;
        $type and $param['type'] = $type;
        $param['page'] = $page ? $page : 1;
        $param['page_size'] = 10;
        $result = $this->httpclient->post($url,$param);
        if($result['status']['code']){
            $pagelist 	= $this->instance('pagelist');	//加载分页类
            $pagelist -> loadconfig();
            $map = array(
                'name' => $name,
                'type' => $type,
                'page' => '{page}'
            );
            $result['pagelist']	= $pagelist -> total($result['total']) -> url(url('admin/phone_bind/index', $map)) -> num($param['page_size'])-> page($param['page'])-> output();
        }else{

        }
        //dump($result);
        $this->view->assign('binding_way',$this->binding_way);
        $this->view->assign('map',$map);
        $this->view->assign('data',$result);
        $this->view->display('admin/phone_bind/all');
    }

    /**
     *
     */
    public function oneAction(){
        $param = $this->param;
        $id = $this->get('id');
        if($this->is_post()){
            
            $param['mobile_name'] = $this->post('mobile_name');
            $param['mobile_type'] = $this->post('mobile_type');
            $param['binding_way'] = $this->post('binding_way');

            if($id){
                $param['id']=$id;
                $url = $this->item('phone_bind','set_one');
            }else{
                if($this->post('batch')){
                    $mobile = preg_replace('/([\S]+)\s+(\S+)[\r\n]*/','$1\t$2\v',$this->post('mobile'));
                    $mobile = explode('\v', $mobile);
                    foreach ($mobile as $k=>$v) {
                        if($v){
                            $temp = explode('\t',$v);
                            $data[$k]['name'] = $temp[0];
                            $data[$k]['type'] = $temp[1];
                        }
                    }
                    $param['mobile'] = json_encode($data);
                    //var_dump($param);
                    $url = $this->item('phone_bind','add_batch');
                }else{
                    $url = $this->item('phone_bind','add_one');
                }
            }
            
            $result = $this->httpclient->post($url,$param);
            if (isset($result) && $result['status']['code'] == 200) {
                $return = array('code'=>1,'info'=>'操作成功');
            } else {
                $return = array('code'=>0,'info'=>'操作失败');
            }
            $this->ajaxReturn($return);
        }else{
            if($id){
                $url = $this->item('phone_bind','get_one');
                $param['id'] = $id;
                $result = $this->httpclient->post($url,$param);
                if($result['status']['code']){
                    $this->view->assign('data',$result['data']);
                }else{

                }
            }else{

            }
            $this->view->assign('binding_way',$this->binding_way);
            $this->view->display('admin/phone_bind/one');
        }

    }

    /**
     *
     */
    public function delAction(){
        $url = $this->item('phone_bind','del_one');
        $id = $this->get('id');
        $param = $this->param;
        $param['ids'] = $id;
        $result = $this->httpclient->post($url, $param);
        //dump($result);
        if($result['status']['code']==200){
            $return = array('code'=>1,'info'=>'删除成功');
        }else{
            $return = array('code'=>0,'info'=>'删除失败');
        }
        $this->ajaxReturn($return);
    }

}