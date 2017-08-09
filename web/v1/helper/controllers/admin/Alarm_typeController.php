<?php
include_once CONTROLLER_DIR.'Cloud.php';
class Alarm_typeController extends Cloud
{
    protected $model;
    protected $types;
    protected $statuses;
    protected $cache;
    protected $cache_id;

    public function __construct()
    {
        parent::__construct();
        $this->statuses = array('禁用','启用');
        $this->cache = new cache_file();
        $this->cache_id = sprintf('cache_alarm_type');
    }

    /**
     *
     */
    public function indexAction()
    {
        //dump($this->getCache());
        $this->allAction();
    }

    /**
     *
     */
    public function getCache(){
        $cache = $this->cache->get($this->cache_id);
        if(empty($cache)){
            $url = $this->item('alarm_type','get_all');
            $param = $this->param;
            $param['pid'] = -1;
            $param['page']=1;
            $param['size']=50;
            $res = $this->httpclient->post($url,$param);
            if($res['status']['code']==200){
                foreach($res['data'] as $k=>$v){
                    if($v['status']){
                        $cache[$v['id']] =$v;
                    }
                }
                $this->cache->set($this->cache_id,$cache);
            }
        }
        return $cache;
    }

    /**
     *
     */
    public function allAction(){
        $type = $this->get('type');
        $page = (int)$this->get('page');
        $size = $this->get('size');

        $type = $type ? $type : -1;
        $page = $page ? $page : 1;
        $size = $size ? $size : 30;

        $url = $this->item('alarm_type','get_all');
        $param = $this->param;
        $param['pid']  = $type;
        $param['page'] = $page;
        $param['size'] = $size;
        $result = $this->httpclient->post($url,$param);
        $temp1 = $result['data'];
        foreach($result['data'] as $k=>&$v){
            if($v['pid']==0) {
                foreach ($temp1 as $kk => $vv) {
                    if ($v['id'] == $vv['pid']) {
                        $v['child'][] = $vv;
                    }
                }
            }else{
                unset($result['data'][$k]);
            }
        }
        unset($v);
        //dump($result);
        //$result['page']=$page;
        //$result['size']=$size;
        //$pagelist = $this->instance('pagelist');
        //$pagelist->loadconfig();
        //$result['pagelist'] =  $pagelist->total($result['count'])->url(url('admin/alarm_type/index',array('page'=>'{page}')))->num($result['size'])->page($result['page'])->output();

        $this->view->assign('data',$result);
        $this->view->assign('status',$this->model->status);
        is_numeric($type) && $this->view->assign('map',array('type'=>$type));
        $this->view->assign('statuses',$this->statuses);
        $this->view->display('admin/alarm_type/all');
    }

    /**
     *
     */
    public function oneAction()
    {
        $id = $this->get('id');
        $param = $this->param;
        if($this->is_post()){//提交
            $url = $this->item('alarm_type','set_one');
            $param['id']=(int)$id;
            $param['data']=$_POST;
            $result = $this->httpclient->post($url,$param);
            if($result['status']['code']==200){
                $return = array('code'=>1,'info'=>'保存成功');
            }else{
                $return = array('code'=>0,'info'=>'保存失败');
            }
            die(json_encode($return));
        }else{
            if($id){
                $url = $this->item('alarm_type','get_one');
                $param['id'] = $id;
                $result = $this->httpclient->post($url,$param);
                $this->view->assign('data',$result['data']);
            }else{

            }
            $this->view->assign('types',$this->getCache());
            $this->view->assign('statuses',$this->statuses);
            $this->view->display('admin/alarm_type/one');
        }
    }

    /**
     *
     */
    public function delAction(){
        $ids = $this->get('ids');
        $url = $this->item('alarm_type','del_any');
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
        $status =(int)$this->get('status');
        $param = $this->param;
        $param['ids'] = $ids;
        $param['data'] = array('status'=>$status);
        $url = $this->item('alarm_type','set_any');
        $result = $this->httpclient->post($url,$param);
        if($result['status']['code']==200){
            $return = array('code'=>1,'info'=>'操作成功');
        }else{
            $return = array('code'=>0,'info'=>'操作失败');
        }
        exit(json_encode($return));
    }
}