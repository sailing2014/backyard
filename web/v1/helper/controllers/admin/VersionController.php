<?php
include_once CONTROLLER_DIR.'Cloud.php';
class VersionController extends Cloud{
    protected $platforms;
    public function __construct() {
		parent::__construct();
        //接口认证方式不同故另外配置
        $time = time();
        $this->param = array(
            'time'       => $time,
            'api_token' => sha1($time."abcdefg")
        );        
        //$this->products = array(1=>'headphone','kidswatch','babycare');
        $this->platforms=array('ios','android','pc','firmware');
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

		$param = $this->param;
		//$pid = $this->get('pid');
        $pid = 3;//默认产品babycare
        $pid and $where[] = "pid = $pid";

        $plat = $this->get('plat');
        $plat and $where[] = "plat like '$plat'";

        $version = $this->get('version');
        $version and $where[] = "version like '%$version%'";

        $page = $this->get('page');
        $page = $page ? $page : 1;

        $size = $this->get('size');
        $size = $size ? $size :10;

		$param['page'] = $page;
        $param['size'] = $size;
        $param['field'] = "*";
        $where and $where = join(' and ',$where);
        $where and $param['where'] = $where;
		$map = array(
				'pid'       => $pid,
				'plat'      => $plat,
				'version'   => $version,
				'page'      => '{page}',
                'size'      => $size,
			);
		$url = $this->item('version','get_all');
		$result = $this->httpclient->post($url,$param);

		if($result['_status']['_code']==200){
            //
            $pagelist 	= $this->instance('pagelist');	//加载分页类
			$pagelist->loadconfig();
			$result['pagelist']	= $pagelist->total($result['_count'])->url(url('admin/version/index', $map))->num($size)->page($page)->output();

		}else{

		}
		$this->view->assign('map',$map);
        $this->view->assign('data',$result);

        $this->view->assign('platforms',$this->platforms);		
		$this->view->display('admin/version/all');
	}

	/**
	 * 
	 */
	public function oneAction(){
		$id = $this->get('id');
        $param = $this->param;
        if($this->is_post()){
            if($id){//编辑
                $param['id']=$id;
                $url = $this->item('version','set_one');
            }else{//保存
                $url = $this->item('version','add_one');
            }
            $param['pid'] = 3;//babycare
            $param['plat'] = $this->post('plat');
            $param['version'] = $this->post('version');
            $param['md5'] = $this->post('md5');
            $param['download'] = $this->post('download');
            $result = $this->httpclient->post($url,$param);

            if($result['_status']['_code']==200){
                $return = array('code'=>1,'info'=>'操作成功');
            }elseif($result['_status']['_code']==400){
                $return = array('code'=>1,'info'=>'数据重复');
            }else {
                $return = array('code'=>0,'info'=>'操作失败');
            }
            $this->ajaxReturn($return);
        }else{

            if($id){//查看
                $param['id'] = $id;
                $url = $this->item('version','get_one');
                $result = $this->httpclient->post($url, $param);
                //dump($result);
                if($result['_status']['_code']==200){
                    $this->view->assign('data',$result['_data']);
                }else{
                    $this->adminMsg(lang('failure'));
                }
            }else{//添加

            }

            $this->view->assign('platforms',$this->platforms);
            $this->view->display('admin/version/one');
        }
    }

	/**
	 * 
	 */
	public function delAction(){
		$url = $this->item('version','del_one');
        $id = $this->get('id');
        $param = $this->param;
        $param['id'] = $id;
        $result = $this->httpclient->post($url, $param);
        if($result['_status']['_code']==200){
           $return = array('code'=>1,'info'=>'删除成功');
        }else{
           $return = array('code'=>0,'info'=>'删除失败');
        }
        $this->ajaxReturn($return);
	}

}