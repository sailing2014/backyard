<?php
include_once CONTROLLER_DIR.'Cloud.php';
class DiaryController extends Cloud{
    protected $types;
    public function __construct() {
		parent::__construct();
        $this->types = array('文字','图片','语音','视频');
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

		$param = $param_1 = $param_2 = $param_3 = $this->param;
		//phone
		$phone = $this->get('phone');
		if($phone){
			$url_1 = $this->item('diary','get_user_by_name');
			$param['username'] = $phone;
			$result_name = $this->httpclient->post($url_1,$param);
			$where[] = "user_id = ".$result_name['user']['uid'];
		}
		//baby_id
		$baby_id = $this->get('baby_id');
		$baby_id and $where[] = "baby_id = ".$baby_id;
		//type
		$type = $this->get('type');
        $type !=="" and $where[] ="d_type = ".$type;
		//page
		$page = $this->get('page') ? $this->get('page') : 1;
		
		$param_1['page'] = $page;
        $param_1['size'] = 10;
		$param_1['field'] = "d_id, user_id, baby_id, d_time,d_img_url,d_video_url,d_voice_url, is_show,d_type";
		$where and $where = join(' and ',$where);
		$where and $param_1['where'] = $where;

		$map = array(
				'phone'	=> urlencode($phone),
				'baby_id'	=> $baby_id,
//				'type'		=> $type,
				'page'		=> '{page}',
			);
        $type !=="" and $map['type'] = $type;
        $url_1 = $this->item('diary','get_all');
		$result = $this->httpclient->get($url_1,$param_1);
		if($result['status']['code']==200 && $result['data']['data']){

			//relation
			$url_2 = $this->item('diary','get_relation_by_baby_id');
			foreach ($result['data']['data'] as &$v) {
				$uids[] 				= $v['user_id'];
                $param_2['uid'] 		= $v['user_id'];
                $param_2['entity_id'] 	= $v['baby_id'];
                $entity	= $this->httpclient->post($url_2,$param_2);
                $entity['status']['code'] ==200 and $v['relation'] = $entity['entities']['entity_relation'];
            }
            unset($v);

			//phone
			if($uids){
				$url_3 				= $this->item('diary','get_user_by_uids');
				$param_3['uids'] 	= join(',',array_unique($uids));
	            $result_uids 		= $this->httpclient->post($url_3,$param_3);            
	            foreach ($result['data']['data'] as &$vv) {
	                $vv['phone'] = $result_uids['users']['user::'.$vv['user_id']]['phone'];
	            }
	            unset($vv);
	            //dump($result_uids);
			}
            //
            $pagelist 	= $this->instance('pagelist');	//加载分页类
			$pagelist->loadconfig();
			//dump($map);
			$result['data']['pagelist']	= $pagelist->total($result['data']['count'])->url(url('admin/diary/index', $map))->num($result['data']['size'])->page($result['data']['page'])->output();

		}else{

		}
		//dump($result['data']);
		unset($map['page']);
		$this->view->assign('map',$map);
		$this->view->assign('types',$this->types);
		$this->view->assign('data',$result['data']);
		$this->view->display('admin/diary/all');
	}

	/**
	 * 
	 */
	public function oneAction(){
		$id = $this->get('id');
        $param = $param1 = $this->param;
        if($this->is_post()){
            if($id){//编辑

            }else{//保存

            }
        }else{
            if($id){//查看
                $param['id'] = $id;
                $url = $this->item('diary','get_one');
                $result = $this->httpclient->get($url, $param);
                //dump($result);
                if(isset($result['status']) && $result['status']['code']==200){
                    $url_1 = $this->item('diary','get_entity_info');
                    //$param['uid'] = '';
                    $param1['entity_id'] = $result['data']['baby_id'];
                    $result_entity = $this->httpclient->post($url_1,$param1);
                    $result['data']['baby'] = $result_entity['user'][0]['name'];
                    //
                    $types = array('d_video_url'=>'视频','d_voice_url'=>'语音','d_img_url'=>'图片');
                    foreach ($types as $k => $v) {
                        if(!empty($result['data'][$k])){
                            $file['type'] = $v;
                            $file['path'] = $result['data'][$k];
                            $file['num'] = count($result['data'][$k]);
                        }
                    }
                    $result['data']['file'] = $file;
                    $this->view->assign('data',$result['data']);
                    $this->view->display('admin/diary/one');
                }else{
                    $this->adminMsg(lang('failure'));
                }
            }else{//添加

            }
        }
    }

	/**
	 * 
	 */
	public function delAction(){
		$url = $this->item('diary','del_one');
        $id = $this->get('id');
        $this->param['id'] = $id;
        $result = $this->httpclient->get($url, $this->param);
        if(isset($result['status']) && $result['status']['code']==200){
           $return = array('code'=>1,'info'=>'删除成功');
        }else{
           $return = array('code'=>0,'info'=>'删除失败');
        }
        $this->ajaxReturn($return);
	}

	/**
	 * 
	 */
	public function set_hideAction(){
		$id = $this->get('id');
        $this->param['id'] = $id;
        $url = $this->item('diary','set_hide');
        $result = $this->httpclient->get($url,$this->param);
        if(isset($result['status']) && $result['status']['code']==200){
           $return = array('code'=>1,'info'=>'屏蔽成功');
        }else{
           $return = array('code'=>0,'info'=>'屏蔽失败');
        }
        $this->ajaxReturn($return);
	}

	/**
	 * 
	 */
	public function set_showAction(){
		$id = $this->get('id');
        $this->param['id'] = $id;
        $url = $this->item('diary','set_show');
        $result = $this->httpclient->get($url,$this->param);
        if(isset($result['status']) && $result['status']['code']==200){
           $return = array('code'=>1,'info'=>'展示成功');
        }else{
           $return = array('code'=>0,'info'=>'展示失败');
        }
        $this->ajaxReturn($return);
	}

}