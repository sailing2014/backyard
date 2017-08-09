<?php
include_once CONTROLLER_DIR.'Cloud.php';
//include_once EXTENSION_PATH.'/Httpful/Bootstrap.php';
class ClientController extends Cloud{
    protected $statuslist;
    public function __construct() {
		parent::__construct();

        //\Httpful\Bootstrap::init();

        $this->statuslist = array(1=>'正常',0=>'禁用');
	}

	public function indexAction(){
        $this->allAction();
    }

    public function allAction(){

        $param = $this->param;
        $uids = $this->get('uids');
        //$nickname =  $this->get('nickname');
        $name = $this->get('name');
        $page = $this->get('page');

        $status = $this->get('status');

        $start = $this->get('start');
        $start = is_numeric($start) ? $start : strtotime($start);
        $start = $start ? $start : 0;

        $end = $this->get('end');
        $end = is_numeric($end) ? $end : strtotime($end);
        $end = $end ? $end : time();

        if($uids){//根据用户uid查询
            $param['uids'] = $uids;
            $url = $this->item('user','get_one_by_uids');
            $result = $this->httpclient->post($url, $param);
            if (isset($result['_status']) && $result['_status']['_code'] == 200) {
                $this->view->assign('data',$result['users']);
            }else{

            }
            $map['uids'] = $uids;
        }elseif(!empty($name)){//根据用户手机或者邮箱
            $param['username'] = $name;
            $url = $this->item('user','get_one_by_name');
            $result = $this->httpclient->post($url, $param);
            if (isset($result['_status']) && $result['_status']['_code'] == 200) {
                $this->view->assign('data',array( $result['user']) );
            }else{

            }
            $map['name'] = $name;
        }else{

            $status != '' and $where['status'] = (int)$status;
            $start || $end and $where['reg_time'] = array( 'start' => (int)$start, 'end'=>(int)$end);
            $param['condition'] = $where;
            $param['page'] = $page ? $page : 1;
            $param['page_size'] = 10;

            $url = $this->item('user','get_all');
            $result = $this->httpclient->post($url, $param,false,array('Content-Type:application/json'));

            if (isset($result['_status']) && $result['_status']['_code'] == 200) {
                //
                foreach($result['data']['rows'] as $k =>$v){
                    $result['list'][$k] = $v['value'];
                }
                $pagelist 	= $this->instance('pagelist');	//加载分页类
                $pagelist->loadconfig();
                $map = array(
                    'uid' => $uid,
                    'start'=>$start,
                    'end' => $end,
                    'status'=>$status,
                    'page'=>'{page}'
                );
                $result['pagelist']	= $pagelist -> total($result['data']['total_rows']) -> url(url('admin/client/index', $map)) -> num($param['page_size'])-> page($param['page'])-> output();

            } else {
                $result['message'] = "操作失败";
            }
            //dump($result);
            $this->view->assign('data',$result['list']);
            $this->view->assign('pagelist',$result['pagelist']);
        }
        //dump($param);
        //dump($map);
        $this->view->assign('map',$map);
        $this->view->assign('statuslist',$this->statuslist);
        $this->view->display('admin/client/all');
    }

}