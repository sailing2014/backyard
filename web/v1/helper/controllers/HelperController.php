<?php
require APP_ROOT . './core/Lunar.php';
include_once CONTROLLER_DIR.'PostAPI.php';
class HelperController extends PostAPI { //继承公共控制器类
//  private $api_list = null;
  public function __construct() {
   parent::__construct();
//   $this->api_list = \Config::get('api_list');
  }
  
/**
 * 获取一级类别
 */
  public function getCatalogAction() { //控制器方法     
      
            $online = false;
            $uid = $this->get('uid');
            $device_type = $this->get('device_type');
            if($uid)
            {
                $device_type = empty($device_type)? 172 :  intval($device_type); //默认空气净化器type=172               
                $online = $this->getDeviceStatus($uid,$device_type); 
            }

            $response["status"]["code"] = 200;
            $response["status"]["message"] = "request success";
            $data = $this->category->getAll("`parentid`=0 and `ismenu`=1 and `typeid`=1", 
                                             $value=null, 'catid,catname,image,listorder');  
            if($online)
            {
                 $data_172 = $this->category->getAll("`parentid`=0 and `ismenu`=1 and `url`='device_type=$device_type'", 
                                                     $value=null, 'catid,catname,image,listorder');           
                 $data = array_merge($data,$data_172);
            }

            foreach ($data as $key => $value) {
                if(isset($data[$key]["image"]) && !empty($data[$key]["image"])){
                    $data[$key]["image"] = thumb($data[$key]["image"]);
                }
            }

            $response["data"] = $data;
            echo(json_encode($response));
  }
  
  /**
   * 获取子类类别
   */
  public function getChildCatalogAction(){
      $response["status"]["code"] = 200;
      $response["status"]["message"] = "request success";
      $catid = (int)$this->get('catid');
      $data = array();
      if($catid){
          $arrchildid = $this->category->getOne("`catid`=$catid", $value = null, 'arrchildid');
          if(!empty($arrchildid["arrchildid"])){       
              $data = $this->category->getAll("catid in "."(".$arrchildid["arrchildid"].")", $value=null, 'catid,catname,image,listorder');
              if(!empty($data)){
                    foreach ($data as $key => $value) {
                     if(isset($data[$key]["image"]) && !empty($data[$key]["image"])){
                         $data[$key]["image"] = thumb($data[$key]["image"]);
                     }
                 }   
              }
          }
      }else{
          $response["status"]["code"] = 400;
          $response["status"]["message"] = "parameter incomplete";
      }
      $response["data"] = $data;
      echo (json_encode($response));
  }
  
    protected function getDeviceStatus($uid,$device_type) {
        $ret = false;
        $url = $this->item('device_info','get_device_binding');       
        $param = $this->param;
        $param += array("uid"=>$uid,"type"=>$device_type);            
        $result = $this->httpclient->post($url,$param);  
//        var_dump(json_encode($result));exit;
        if( 200 == $result["status"]["code"] )
        {
            if(isset($result['devices']) && !empty($result['devices']))
            {                
                $ret = true;                       
            }
            
        }
     
        return $ret;
    }
  
  /**
   * 获取文章列表
   */
  public function getListAction(){
      $response["status"]["code"] = 200;
      $response["status"]["message"] = "request success";
      $catid = (int)$this->get('catid');
      $content_switch = (int)$this->get('content_switch');
      $pics_switch = (int)$this->get('pic');
      $param_week = (int)$this->get('week');
      $week = $param_week > 52 ? 52 : $param_week;
      
      $param_month = (int)$this->get('month');
      $month = $param_month > 12 ? 12 : $param_month;
      
      $vaccinetime = (int)$this->get('vaccinetime'); 
      $vaccinetime = $vaccinetime > 365 ? 360 : $vaccinetime;
      $vaccinetime = $vaccinetime <= 0   ? 0   : $vaccinetime;
      $vaccineflag = $this->isVaccineCat($catid);
      
      $page = (int)$this->get('page');
      $pagesize = (int)$this->get('pagesize');
      $param = "catid=$catid ";
      if($page){
          $param .="page=$page ";
      }
      if($pagesize){
          $param .="pagesize=$pagesize ";
      }
      
      $order = "updatetime";
      if($vaccineflag){                    
          $param .="_vaccinetime=$vaccinetime ";
          $order = "vaccinetime";               
      }    
      
      $param .= "urlrule=/index.php?c=content&a=list&catid=$catid&page=[page] order=$order more=1";
      $out = array();
      $out_filter = array();
      $vaccinetime_arr = array();
      if($catid){
          $data = $this->view->_listdata($param);
          $out = $data["return"];             
//          echo (json_encode($out));          exit();
          foreach($out as $key => $value){
                if($content_switch){
                    unset($out[$key]["content"]);
                }
                if(isset($out[$key]['thumb']) && !empty($out[$key]['thumb'])){
                $out[$key]['thumb'] = thumb($out[$key]['thumb']);
                }
                if(isset($out[$key]['url'])){                  
//                $out[$key]['url'] = $this->api_list['content']['url'].$out[$key]["id"];   
                    $content_url = $this->item('content', 'url');
                    if($content_url)
                    {
                        $out[$key]['url'] = $content_url.$out[$key]["id"];   
                    }
                }
                if($week){ //筛选选周数相等的数据
                    if(isset($out[$key]['week']) && $week == intval($out[$key]['week'])){
                        $out_filter[]=$out[$key];
                    }
                }
                if($month){ //筛选月份数相等的数据
                    if(isset($out[$key]['month']) && $month == intval($out[$key]['month'])){
                       $out_filter[]=$out[$key];
                    }
                }
                if ($pics_switch) {
                    $j = $key + 1;
                    $newdata[$key]["id"] = $j;
                    $newdata[$key]["title"] = $value['title'];
                    $newdata[$key]["images"] = $value['thumb'];
                    $newdata[$key]["url"] = $value['picurl'];
                }                
          }             
      }else{
          $response["status"]["code"] = 400;
          $response["status"]["message"] = "parameter incomplete";
      }
      
      if($week || $month ){       
        $response["data"] = $out_filter;
      } else if ($pics_switch){
        $response["data"] = $newdata;
      } else {          
            $response["data"] = $out;
      }
      echo (json_encode($response));
  }
  
  /**
   * 获取文章内容
   */
  public function getContentAction(){
            $response["status"]["code"] = 200;
            $response["status"]["message"] = "request success";
            $id		= (int)$this->get('id');
            if($id){
	    $page	= $this->get('page') ? (int)$this->get('page') : 1;
	    $data	= $this->content->find($id);	//查询当前文档数据

	    $model	= $this->get_model();	//获取模型缓存
	    $catid	= $data['catid'];	//赋值栏目id
	    $cat	= $this->cats[$catid];	//获取当前文档的栏目数据
	    if (  (!empty($data)) && ($data['status'] !== 0) 	//判断数据是否存在或文档状态是否通过
		  && (isset($model[$data['modelid']]) && !empty($model[$data['modelid']])) 	//判断模型是否存在
		  && (!empty($cat))	//判断栏目是否存在
                ){
                    $table	= $this->model($model[$data['modelid']]['tablename']);
                    $_data	= $table->find($id);	//附表数据查询
                    $data	= array_merge($data, $_data); //合并主表和附表
                    $data	= $this->getFieldData($model[$cat['modelid']], $data);	//格式化部分数据类型
                    $data	= $this->get_content_page($data, 1, $page);	//内容分页和子标题
                    if(isset($data["thumb"]) && !empty($data["thumb"])){
                        $data["thumb"] = thumb(($data["thumb"]));
                    }

//                    $data["url"] = $this->api_list['content']['url'].$id;
                    $content_url = $this->item('content', 'url');
                    if($content_url)
                    {
                        $data["url"] = $content_url.$id;
                    }
                    //统计点击
                    $this->content->update(array('hits' => 'hits+1'), 'id=' . $id);
                }
            }else{
                $response["status"]["code"] = 400;
                $response["status"]["message"] = "parameter incomplete";
            }
                
            $response["data"] = $data?$data:array();
            echo (json_encode($response));
  }
 
        /**
	 * 内容搜索
	 */
	public function searchAction() {
                $response["status"]["code"] = 200;
                $response["status"]["message"] = "request success";
		$kw    = $this->get('kw');
		$kw    = urldecode($kw);
		$sql   = null;
		$page  = (int)$this->get('page') > 0 ? (int)$this->get('page') : 1;  
                $pagesize = (int)$this->get('pagesize') > 0 ? (int)$this->get('pagesize') : 10;
                $param = $this->getParam();                
		if ($this->site['SITE_SEARCH_TYPE'] == 2) {
		    //Sphinx
			if (empty($kw)) $this->msg(lang('con-5'));
                        App::auto_load('sphinxapi');
                        $cl   = new SphinxClient ();
			$host = $this->site['SITE_SEARCH_SPHINX_HOST'];
			$prot = $this->site['SITE_SEARCH_SPHINX_PORT'];
			$name = $this->site['SITE_SEARCH_SPHINX_NAME'];
//			$start= ($page - 1) * (int)$this->site['SITE_SEARCH_PAGE'];
//			$limit= (int)$this->site['SITE_SEARCH_PAGE'];
                        $start= ($page - 1) * $pagesize;
			$limit= $pagesize;
                        $cl->SetServer($host, 9312);
                        $cl->SetMatchMode(SPH_MATCH_ALL);
                        $cl->SetSortMode(SPH_SORT_EXTENDED, 'updatetime DESC');
                        $cl->SetLimits($start, $limit);
			$res = $cl->Query($kw, $this->site['SITE_SEARCH_SPHINX_NAME']);
			if ($res['total']) {
			    $ids     = '';
				foreach ($res['matches'] as $cid=>$val) {
				    $ids .= $cid . ',';
				}
				$ids     = substr($ids, -1) == ',' ? substr($ids, 0, -1) : $ids;
			    $total   = $res['total'];
				$pageurl = $this->site['SITE_SEARCH_URLRULE'] ? str_replace('{id}', urlencode($kw), $this->site['SITE_SEARCH_URLRULE']) : url('content/search', array('kw' => urlencode($kw), 'page' => '{page}'));
				$sql     = 'SELECT id,modelid,catid,url,thumb,title,keywords,description,username,updatetime,inputtime from ' . $this->content->prefix . 'content_' . $this->siteid . ' WHERE id IN (' . $ids . ') ORDER BY updatetime DESC LIMIT ' . $limit;
			}
		} else {
		    //普通搜索
                        $search  = $this->model('search');                       
                        $start= ($page - 1) * $pagesize;
			$limit= $pagesize;
			$cache   = (int)$this->site['SITE_SEARCH_INDEX_CACHE']; 
                        $result  = $search->getData((int)$this->get('id'), $cache, $param, $start, $limit, $this->site['SITE_SEARCH_KW_FIELDS'], $this->site['SITE_SEARCH_KW_OR']);                                               
                        $kw      = $result['keywords'];
			$sql     = $result['sql'];
			$total   = $result['total'];
			$catid   = $result['catid'];
			$modelid = $result['modelid'];
			$pageurl = $this->site['SITE_SEARCH_URLRULE'] ? str_replace('{id}', $result['id'], $this->site['SITE_SEARCH_URLRULE']) : url('content/search', array('id' => $result['id'], 'page' => '{page}'));
		}
                
		if ($sql) {
			$pagelist = $this->instance('pagelist');
			$pagelist->loadconfig();
			$data     = $this->content->execute($sql, true, $this->site['SITE_SEARCH_DATA_CACHE']);
			$pagelist = $pagelist->total($total)->url($pageurl)->num($this->site['SITE_SEARCH_PAGE'])->page($page)->output();
                } else {
                        $data     = array();
			$total    = 0;
			$pagelist = '';
		}
	  
	    
            $out =    array(
			'kw'         => $kw,
                        'searchpage' => $pagelist,
                        'searchdata' => $data,
			'searchnums' => $total
	    );      
            
            if($data){
                foreach($data as $key => $value){              
                    if(isset($data[$key]['thumb']) && !empty($data[$key]['thumb'])){
                    $data[$key]['thumb'] = thumb($data[$key]['thumb']);
                    }
                    if(isset($data[$key]['url']) && !empty($data[$key]['url'])){
                        $content_url = $this->item('content', 'url');
                        if($content_url)
                        {
                            $data[$key]['url'] = $content_url.$data[$key]["id"];
                        }
                    }
              } 
            }           
            
            $response["data"] = $data;
            echo (json_encode($response));
	}
        
       function getConstellationAction(){
           $response["status"]["code"] = 200;
           $response["status"]["message"] = "request success";
           $data = array();
           
           $birthday = trim($this->get('birthday'));
           $bloodtype = trim($this->get('bloodtype'));
           
           if($birthday){
                $birthday_arr = explode('-', $birthday);
                $zodiac_birthday = "2001-".$birthday_arr[1]."-".$birthday_arr[2]; //转换成2001年，与数据库数据一致
                $zodiac_date = strtotime($zodiac_birthday);          
                $zodiac_data = $this->getConstellationData($zodiac_date);
                $data["zodiac"] = $zodiac_data;

                $animal_data = $this->getAnimalData($birthday);
                $data["animal"] = $animal_data;    
            }
            
            if($bloodtype){
                $bloodtype_data = $this->getBloodTypeData($bloodtype);
                $data["bloodtype"] = $bloodtype_data; 
            }
            
            if($data){
                foreach($data as $key => $value){         
                    if(isset($data[$key]['thumb']) && !empty($data[$key]['thumb'])){
                    $data[$key]['thumb'] = thumb($data[$key]['thumb']);
                    }
                    if(isset($data[$key]['url']) && !empty($data[$key]['url'])){
                        $content_url = $this->item('content', 'url');                        
                        if($content_url)
                        {
                            $data[$key]['url'] = $content_url.$data[$key]["id"];
                        }
                    }
              } 
            }    
            
            $response["data"] = $data;
            echo (json_encode($response));
       }
       
    protected function getConstellationData($zodiac_date){
            
            $zodiac_data = array();
            
            $param = "catid=83 ";     
            $param .= "urlrule=/index.php?c=content&a=list&catid=83&page=[page] order=updatetime more=1";
            
            $data = $this->view->_listdata($param);            
            $return = $data["return"];
            if(!empty($return)){
                    foreach($return as $value){
                        if( ( $zodiac_date >=  intval($value["startdate"]) )  && ( $zodiac_date <=  intval($value["enddate"]) ) ){
                            $zodiac_data = $value;                        
                            break;
                        }                  
                    }                
                    if(empty($zodiac_data)){
                        foreach($return as $value){
                        if(intval($value["zodiac"]) == 12){
                            $zodiac_data = $value;                        
                            break;
                        }                  
                    }
                }
            }
            
            
            
            return $zodiac_data;
       }
       
       protected function getAnimalData($birthday){
           $lunar = new Lunar();
            //公历转农历
//            $nl = date("Y-m-d",$lunar->S2L($birthday));          
//            $nl_arr = explode('-', $nl);
//            var_dump($nl_arr);
           $nl_arr = $lunar->S2L($birthday);
           
            //农历转公历
//            $gl = date("Y-m-d",$lunar->L2S($nl));    
            $animal = $lunar->get_animal($nl_arr["Lyear"]);
//            echo "生日是:$birthday<br/>";
//            echo "转为农历是:$nl<br/>";
//            echo "转回公历是:$gl<br/>";      
//            echo "所属生肖是:$animal<br/>";    
            $animal_data = array();
            
            $param = "catid=85 ";     
            $param .= "urlrule=/index.php?c=content&a=list&catid=85&page=[page] order=updatetime more=1";
            
            $data = $this->view->_listdata($param);
            $return = $data["return"];            
            if(!empty($return)){
                foreach($return as $value){
                    if($animal == intval($value['animal'])){
                        $animal_data = $value;
                        break;
                    }
                }
            }
            $animal_data["lunarday"]=$nl_arr["Ldate"];
            return $animal_data;
       }
       
       protected function getBloodTypeData($bloodtype){
            $blood_data = array();
            
            $param = "catid=84 ";     
            $param .= "urlrule=/index.php?c=content&a=list&catid=84&page=[page] order=updatetime more=1";
            
            $data = $this->view->_listdata($param);
            
            $return = $data["return"];     
            
            if(!empty($return)){
                foreach($return as $value){
                    if($bloodtype == $value['bloodtype']){
                        $blood_data = $value;
                        break;
                    }
                }
            }
            
            return $blood_data;
       }
       
       /**
	  * 更新浏览数
	  */
	public function hitsAction() {
                $response["status"]["code"] = 200;
                $response["status"]["message"] = "request success";
                $out = array();
                
                $id   = (int)$this->get('id');
		if (!empty($id)){	
                    $data = $this->content->find($id, 'hits');                    
                    if (!empty($data)) {
                        $hits = $data['hits'];
                        $this->content->update(array('hits' => 'hits+1'), 'id=' . $id);                        
                    }                    
                }
             
                $out["id"] = empty($id)?"":$id;
                $out["hits"] = empty($hits)? "":$hits + 1;
                
                $response["data"] = $out;                
                echo (json_encode($response));
	}
        
        /**
         * 查看是否是疫苗或体检catid，是返回true，否则false
         * 
         * @param int $catid
         * @return boolean
         */
        protected function isVaccineCat($catid){
            $ret = false;
            $vaccine_catid = $this->item('category', 'vaccine');
            $check_catid = $this->item('category','check');
            if(in_array($catid, array($vaccine_catid,$check_catid))){
                $ret = true;
            }
            return $ret;
        }
        
        public function getAdvertAction(){
            $response["status"]["code"] = 200;
            $response["status"]["message"] = "request success";          
            
            $in = $this->item("category", "intl_ad");
            $in_data = $this->addDataField($in,"type",1); 
            $out = $this->item("category", "outer_ad");            
            $out_data = $this->addDataField($out, "type");           
            $response["data"] = array_merge($in_data,$out_data);    
            echo (json_encode($response));
        }
        
        protected function addDataField($catid,$field="type",$type=0,$value=0){
            $param = "catid=$catid ";     
            $param .= "urlrule=/index.php?c=content&a=list&catid=$catid&page=[page] order=updatetime more=1";
            $data = $this->view->_listdata($param);  
            
            $out = array();
             foreach ($data["return"] as $key => $val){
                $out[$key]["id"] = $val["id"];
                $out[$key]["catid"] = $val["catid"];
                $out[$key]["title"] = $val['title'];
                $out[$key]["images"] = $val['thumb'];
                $out[$key]["url"] = $val['picurl'];
                if($type){
                $out[$key][$field] = $this->getTypeByUrl($val["picurl"], $value);
                }else{
                    $out[$key][$field] = $value;
                }
            }
            return $out;
        }
        
        protected function getTypeByUrl($url,$value){
            $value = 100;
            if(false !== strpos($url, "angel")){
                $value = 100;
            }else if(false !== strpos($url,"phone")){
                $value = 101;
            }
            return $value;
        }
}