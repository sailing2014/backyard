<?php
include_once CONTROLLER_DIR.'Cloud.php';
class ScoreController extends Cloud
{

    public function __construct()
    {
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
        $page = $this->get('page');
        $size = $this->get('size');

        $page = $page ? $page :1;
        $size = $size ? $size :10;

        $url = $this->item('score','list');
        $param = $this->param;
		$param["view_table"]="point_rule";
		$status["status"] =1;
		$param["condition"]=$status;
        $result = $this->httpclient->post($url,$param,$multi = false,array("Content-Type:application/json"));
        if($result['_status']['_code']==200){
            $result['page']=$page;
            $result['size']=$size;
            $pagelist = $this->instance('pagelist');
            $pagelist->loadconfig();
            $result['pagelist'] =  $pagelist->total($result['count'])->url(url('admin/blog_tags/index',array('page'=>'{page}')))->num($result['size'])->page($result['page'])->output();

        }else{

        }

		/*$result[0]["id"]=1;
		$result[0]["name"]="注册";
		$result[0]["rule_id"]="register";
		$result[0]["count"] =100;
		$result[0]["day_count_limit"]=500;
		$result[0]["description"]="注册内容";*/
        $this->view->assign('data',$result["data"]["rows"]);
        $this->view->display('admin/score/all');
    }


    /**
     *
     */
    public function oneAction()
    {
        $rule_id = $this->get('rule_id');
        $param = $this->param;
        if($this->is_post()){//添加或者修改

			 $url = $this->item('score','rule_add');
			 $get_url = $this->item('score','rule_get');
			 $param['rule_id'] = $_POST["rule_id"];
			 
			 $get_result = $this->httpclient->post($get_url,$param);
			 
			 if($rule_id){
					$param['name'] = $_POST["name"];
					$param['count'] = $_POST["count"];
					$param['day_count_limit'] = $_POST["day_count_limit"];
					$param['description'] = $_POST["description"];
					$result = $this->httpclient->post($url,$param);
					if ($result['_status']['_code'] == 200) {
						$return = array('code'=>1,'info'=>'修改成功');
					}else {
						$return = array('code'=>0,'info'=>'修改失败');
					}
			 }else{
				if($get_result['_status']['_code']==200){
				$return = array('code'=>0,'info'=>'rule_id已经存在');
				 }else{
					$param['name'] = $_POST["name"];
					$param['count'] = $_POST["count"];
					$param['day_count_limit'] = $_POST["day_count_limit"];
					$param['description'] = $_POST["description"];
					$result = $this->httpclient->post($url,$param);
					if ($result['_status']['_code'] == 200) {
						$return = array('code'=>1,'info'=>'添加成功');
					} else {
						print_r($result);
						//$return = array('code'=>0,'info'=>'添加失败');
					}
				 }
			 
			 }
			 
  
            $this->ajaxReturn($return);
        }else{
            if($rule_id){//查看
                $url = $this->item('score','rule_get');
				$param['rule_id'] = $rule_id;
                $result = $this->httpclient->post($url,$param);
                if($result['_status']['_code']==200){
                    $this->view->assign('data', $result['data']);
                }else{

                }
            }else{//添加

            }
            $this->view->display('admin/score/one');
        }
    }


    /**
     *
     */
    public function delAction(){
        $ids = $this->get('ids');
        $url = $this->item('score','del_any');
        $param = $this->param;
        $param['ids']= $ids;
        $result = $this->httpclient->post($url,$param);
        if($result['status']['code']==200){
            $return = array('code'=>1,'info'=>'删除成功');
        }else{
            $return = array('code'=>0,'info'=>'删除失败');
        }
        exit(json_encode($return));
    }

    /**
     *
     */
    public function setAction(){
        $ids = $this->get('ids');
        $pid =(int)$this->get('pid');
        $type_id =(int)$this->get('type_id');
        $param = $this->param;
        $param['ids'] = $ids;
        $param['data'] = array('pid'=>$pid,'type_id'=>$type_id);
        $url = $this->item('score','set_any');
        $result = $this->httpclient->post($url,$param);
        if($result['status']['code']==200){
            $return = array('code'=>1,'info'=>'操作成功');
        }else{
            $return = array('code'=>0,'info'=>'操作失败');
        }
        exit(json_encode($return));
    }

}
