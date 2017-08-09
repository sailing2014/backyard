<?php
include_once CONTROLLER_DIR.'Cloud.php';
class Alarm_infoController extends Cloud
{
    protected $levels;
    protected $methods;

    protected $types;


    public function __construct()
    {
        parent::__construct();
        $this->levels = array('','普通','中等','严重');
        $this->methods = array(
            'push'      => 'push消息',
            'msg'       => '短信消息',
            'notice'    => '站内消息');
        $this->import(CONTROLLER_DIR.'admin/Alarm_typeController.php');
        $Alarm_type = new Alarm_typeController();
        $this->types = $Alarm_type->getCache();
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
        $type_id = $this->get('type_id');

        $page = $page ? $page :1;
        $size = $size ? $size :10;
        $type_id = is_numeric($type_id) ? $type_id : -1;

        $url = $this->item('alarm_info','get_all');
        $param = $this->param;
        $param['type_id'] = $type_id;
        $param['page']= $page;
        $param['size']= $size;
        $result = $this->httpclient->post($url,$param);
        if($result['status']['code']==200){
            foreach($result['data'] as &$v){
                $v['method'] = json_decode($v['method'],true);
            }
            unset($v);
//            dump($result);
            $result['page']=$page;
            $result['size']=$size;
            $pagelist = $this->instance('pagelist');
            $pagelist->loadconfig();
            $result['pagelist'] =  $pagelist->total($result['count'])->url(url('admin/alarm_info/index',array('type_id'=>$type_id,'page'=>'{page}')))->num($result['size'])->page($result['page'])->output();

        }else{

        }

        $this->view->assign('levels',$this->levels);
        $this->view->assign('methods',$this->methods);
        $this->view->assign('data',$result);
        $this->view->assign('types',$this->types);
        $this->view->assign('type_id',$type_id);
        $this->view->display('admin/alarm_info/all');
    }


    /**
     *
     */
    public function oneAction()
    {
        $id = $this->get('id');
        $param = $this->param;
        if($this->is_post()){//添加或者修改

            $url =$this->item('alarm_info','set_one');
            $param['id']= (int)$id;
//            $param['level'] = $this->post('level');
//            $param['reason']= $this->post('reason');
//            $param['content'] = $this->post('content');
            $temp = array('push'=>0,'msg'=>0,'notice'=>0);
            $method =$this->post('method');
            foreach($temp as $k=>$v){
                $method[$k] and $temp[$k] = (int)$method[$k];
            }
//            $param['method'] = json_encode($temp);
            $param['data'] = array(
                'pid'=>$this->post('pid'),
                'type_id'=>$this->post('type_id'),
                'reason'=>$this->post('reason'),
                'content'=> $this->post('content'),
                'method'=>json_encode($temp),
                'level'=>$this->post('level'),
                'vaccine_id'=>$this->post('vaccine_id')
            );
            $result = $this->httpclient->post($url,$param);
            if ($result['status']['code'] == 200) {
                $return = array('code'=>1,'info'=>'操作成功');
            } else {
                $return = array('code'=>0,'info'=>'操作失败');
            }
            $this->ajaxReturn($return);

        }else{
            if($id){//查看
                $url = $this->item('alarm_info','get_one');
                $param['id'] = (int)$id;
                $result = $this->httpclient->post($url,$param);
                if($result['status']['code']==200){
//                    $result['info'][0]['method'] = json_decode($result['info'][0]['method'],true);
                    $result['data']['method'] = json_decode($result['data']['method'],true);
                }else{

                }
            }else{//添加

            }
            $this->view->assign('levels',$this->levels);
            $this->view->assign('methods',$this->methods);            
            $this->view->assign('data', $result['data']);
            $this->view->assign('types',$this->types);
            $this->view->display('admin/alarm_info/one');
        }
    }


    /**
     *
     */
    public function delAction(){
        $ids = $this->get('ids');
        $url = $this->item('alarm_info','del_any');
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
        $url = $this->item('alarm_info','set_any');
        $result = $this->httpclient->post($url,$param);
        if($result['status']['code']==200){
            $return = array('code'=>1,'info'=>'操作成功');
        }else{
            $return = array('code'=>0,'info'=>'操作失败');
        }
        exit(json_encode($return));
    }

}