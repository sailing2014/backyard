<?php
include_once CONTROLLER_DIR.'Cloud.php';
class BabyController extends Cloud{
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
        $url = $this->item('baby','get_all');
        $param = $param1 = $this->param;
        $uid = $this->get('uid');
        $baby_id = $this->get('baby_id');
        $page = $this->get('page');

        $param['uid'] = $uid ? $uid : 0;
        $param['entity_id'] = $baby_id ? $baby_id : 0;
        $param['page'] = $page ? $page : 1;
        $param['size'] = 10;
        $result = $this->httpclient->post($url,$param);
        if($result['count']){
            foreach($result['user'] as &$v){
                $ids[] = $v['id'];
            }
            unset($v);
            $url1 = $this->item('baby','get_device');
            $param1['entity_ids'] = join(',',$ids);
            $devices = $this->httpclient->post($url1,$param1);
            $this->view->assign('devices',$devices['data']);

            $pagelist 	= $this->instance('pagelist');	//加载分页类
            $pagelist -> loadconfig();
            $map = array(
                'uid'        => $uid,
                'entity_id' => $baby_id,
                'page'       => '{page}'
            );
            //dump($map);
            $result['pagelist']	= $pagelist -> total($result['count']) -> url(url('admin/baby/index', $map)) -> num($param['size'])-> page($param['page'])-> output();

        }else{

        }
        //dump($result);
        $this->view->assign('map',$map);
        $this->view->assign('data',$result);
        $this->view->display('admin/baby/all');
    }

    /**
     *
     */
    public function oneAction(){
        $param = $param1 = $param2 = $this->param;
        $id = $this->get('id');
        if($this->is_post()){

        }else{
            if($id){
                $url = $this->item('baby','get_one');
                $param['entity_id'] = $id;
                $result = $this->httpclient->post($url,$param);
                //dump($result);
                if($result['count']){
                    $this->view->assign('data',$result['user'][0]);

                    //devices
                    $url1 = $this->item('baby','get_devices');
                    $param1['entity_ids'] = $id;
                    $devices = $this->httpclient->post($url1,$param1);
                    //dump($devices);
                    $this->view->assign('devices',$devices['entity_devices'][$id]);

                    //friends
                    $url2 = $this->item('baby','get_friends');
                    $param2['entity_id'] = $id;
                    $friends = $this->httpclient->post($url2,$param2);
                    $this->view->assign('friends',$friends['user']);
                    //dump($friends);
                    //
                    $this->view->assign('device_types',$this->cache->get('devices'));
                }else{

                }
            }else{

            }

            $this->view->display('admin/baby/one');
        }




    }

}