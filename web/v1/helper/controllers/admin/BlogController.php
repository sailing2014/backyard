<?php
include_once CONTROLLER_DIR.'Cloud.php';
class BlogController extends Cloud{
    protected $types;
    protected $positions;
    public function __construct() {
		parent::__construct();
        $this->types = array('文字','图片','语音','视频');
        $this->positions = array('未知','首页','看看');
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

		$param_1 = $param_2 = $param_3 = $param_4 = $this->param;

        //phone
        $phone = $this->get('phone');
        if($phone){
            $url_1 = $this->item('diary','get_user_by_name');
            $param_1['username'] = $phone;
            $result_name = $this->httpclient->post($url_1,$param_1);
//            dump($result_name);
            $where[] = "user_id = '".$result_name['user']['uid']."'";
        }
        //type
        $type = $this->get('type');
        $type !=="" and $where[] ="d_type = ".$type;

        //tags
        $tags = $this->get('tags');
        if($tags){
            $param_2['tags'] = $tags;
        }

		//page
		$page = $this->get('page');
        $size = $this->get('size');
		$param_2['page'] = $page ? $page : 1;
        $param_2['size'] = $size ? $size : 10;
		$param_2['field'] = "d_id, position_id, user_id, baby_id,d_content, d_time,d_img_url,d_video_url,d_voice_url, is_show,d_type";

        $where and $where = join(' and ',$where);
		$where and $param_2['where'] = $where;

		$map = array(
				'phone'	=> urlencode($phone),
				'tags'	    => $tags,
				'page'		=> '{page}',
			);
        $type !="" and $map['type'] = $type;
        $url_2 = $this->item('blog_tags','get_diary_by_tags');
		$result = $this->httpclient->post($url_2,$param_2);
//        dump($url_2);
//        dump($param_2);
//        dump($result);
		if($result['status']['code']==200 && $result['data']){
			//
			$url_2 = $this->item('diary','get_relation_by_baby_id');
			foreach ($result['data'] as &$v) {
				$uids[] = $v['user_id'];
                $dids[] = $v['d_id'];
            }
            unset($v);

			//phone
			if($uids){
				$url_3 				= $this->item('diary','get_user_by_uids');
				$param_3['uids'] 	= join(',',array_unique($uids));
	            $result_uids 		= $this->httpclient->post($url_3,$param_3);
	            foreach ($result['data'] as &$vv) {
	                $vv['phone'] = $result_uids['users']['user::'.$vv['user_id']]['phone'];
	            }
	            unset($vv);
	            //dump($result_uids);
			}
            //tags
            if($dids){
                $dids = join(',',$dids);
                $param_4['diary_id'] = $dids;
                $url_4 = $this->item('blog_tags','get_tags_by_diary');
                $result_dids 		= $this->httpclient->post($url_4,$param_4);
//                var_dump($url_4);
//                var_dump($param_4);
//                var_dump($result_dids);
                if(isset($result_dids['data'])){
                    foreach ($result['data'] as &$vv) {
                        $vv['tags'] = $result_dids['data'][$vv['d_id']];
                    }
                    unset($vv);
                }
            }

//            dump($result);
            //
            $pagelist 	= $this->instance('pagelist');	//加载分页类
			$pagelist->loadconfig();
			//dump($map);
			$result['pagelist']	= $pagelist->total($result['count'])->url(url('admin/blog/index', $map))->num($result['size'])->page($result['page'])->output();

		}else{

		}
//		dump($result);
		unset($map['page']);
		$this->view->assign('map',$map);
		$this->view->assign('types',$this->types);
        $this->view->assign('positions',$this->positions);
		$this->view->assign('data',$result);
		$this->view->display('admin/blog/all');
	}

	/**
	 * 
	 */
	public function oneAction(){
		$id = $this->get('id');
        $param = $param1 = $this->param;
        if($this->is_post()){
            if($id){//编辑
                $url = $this->item('diary','set_one');
                $param['id'] = (int)$id;
                $param['data'] = array('position_id'=>$_POST['position_id']);
                $result = $this->httpclient->post($url,$param);
            }else{//保存

            }
            if($result['status']['code']==200){
                $response = array('code'=>1,'info'=>'保存成功');
            }else{
                $response = array('code'=>0,'info'=>'保存失败');
            }
            $this->ajaxReturn($response);
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

                }else{
                    $this->adminMsg(lang('failure'));
                }
            }else{//添加

            }
            $this->view->assign('positions',$this->positions);
            $this->view->display('admin/blog/one');
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