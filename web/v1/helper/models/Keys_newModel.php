<?php
include_once  CONTROLLER_DIR.'models/Content_1_foodModel.php';
class Keys_newModel extends Model {
    /**
     * 根据关键字获取文章列表
     * @param string $keys
     * @param int $page
     * @param int $size
     * @return array
     */
    public function get_content_by_keys($keys,$page,$size){      
        $sql = "SELECT c.id,c.title,c.thumb, c.hits+c.lies as hits from fn_content_1 c WHERE id IN(".
            "SELECT DISTINCT(tc.content_id) FROM fn_keys_content tc LEFT JOIN fn_keys t ON tc.keys_id=t.id WHERE t.name='$keys'".
            ") ORDER BY listorder desc,hits desc LIMIT ".($page-1)*$size.",$size";
//        var_dump($sql);
        $return['data'] = $this->execute($sql);        
        return $return;
    }
    
    /**
     * 
     * @param type $key
     * @param type $month
     * @param type $page
     * @param type $size
     * @return type
     */
    public function get_data_by_key($key,$month,$page=1,$size=10){
        $sql_ret = "";
        $data = array();
        // 1.查询到所有满足条件的content_id
        $sql = "SELECT DISTINCT(tc.content_id) FROM fn_keys_content tc LEFT JOIN fn_keys t ON tc.keys_id=t.id WHERE t.name like '%$key%'";     
        $ret1 = $this->get_id_str($sql, "content_id");
        $id_str = $ret1["id_str"]; 
        
         if($id_str){   
            $sql1 = "SELECT distinct(id) from fn_content_1_mix where id in ($id_str) and "
                        . "(start<=$month or close>=$month)";   
            $mix1= $this->get_id_str($sql1);
            $id_str1 = $mix1["id_str"];
            $count1 = $mix1["count"];
                  
            $sql2 = "SELECT distinct(id) from fn_content_1_mix where id in ($id_str) and "
                        . "(start>$month  and close<$month)  ";
            $mix2 = $this->get_id_str($sql2);
            $count2 = $mix2["count"];   
            $id_str2 = $mix2["id_str"];
            
            $where1 = $id_str1? "(fcm.id in ($id_str1)) and ":"";
            $where2 = $id_str2? "where fcm.id in ($id_str2) and ":""; 
            if($count1 >= $page*$size){                
              
               
                $sql_ret = "SELECT fcm.id,fcm.catid,fcm.tag,fc.title,fc.thumb,fc.description from fn_content_1_mix fcm"
                        . " left join fn_content_1 fc on fcm.id = fc.id where "
                        . "(fcm.start<=$month or fcm.close>=$month) ORDER BY fc.updatetime desc LIMIT " .($page-1)*$size.",".$size;
                
                //2. 先查询满足条件的fn_content_1_mix  如果总数够$page*$size;则 取出$size条 按满足宝宝年龄和更新时间排序
            }else if( $count1 + $count2 >= $page*$size){               
                $sql_ret = "(SELECT fcm.id,fcm.catid,fcm.tag,fc.title,fc.thumb,fc.description from fn_content_1_mix fcm"
                           . " left join fn_content_1 fc on fcm.id = fc.id where ". $where1
                           . "(fcm.start<=$month or fcm.close>=$month) ORDER BY fc.updatetime desc) "."union "
                           ."(SELECT fcm.id,fcm.catid,fcm.tag,fc.title,fc.thumb,fc.description from fn_content_1_mix fcm"
                           . " left join fn_content_1 fc on fcm.id = fc.id ". $where2 
                           . "ORDER BY fc.updatetime desc) LIMIT " .($page-1)*$size.",".$size;
               
            }else{             
                   //3 .  总数不够，从fn_content_1_food里面取 取出剩下不够的 也是按满足宝宝年龄 和更新时间 排序 
                $foodModel = new \Content_1_foodModel();
                $sql_month = $foodModel->get_sql_month($month);
                $mix3 = $this->get_id_str("SELECT distinct(id) FROM fn_content_1_food WHERE (month $sql_month) and (id in ($id_str))");
                $id_str3= $mix3["id_str"];
                $where3 = $id_str3? "WHERE fd.id in ($id_str3)":"";
                $sql_ret = "(SELECT fcm.id,fcm.catid,fcm.tag,fc.title,fc.thumb,fc.description from fn_content_1_mix fcm"
                           . " left join fn_content_1 fc on fcm.id = fc.id where ". $where1
                           . "(fcm.start<=$month or fcm.close>=$month) ORDER BY fc.updatetime desc) "."union "
                           ."(SELECT fcm.id,fcm.catid,fcm.tag,fc.title,fc.thumb,fc.description from fn_content_1_mix fcm"
                           . " left join fn_content_1 fc on fcm.id = fc.id  ".$where2
                           . "ORDER BY fc.updatetime desc) "."UNION "
                           ."(SELECT fd.id,fd.catid,fd.tag,f.title,f.thumb,f.description "
                           ."FROM  fn_content_1_food fd LEFT JOIN fn_content_1 f ON fd.id = f.id  "
                           .$where3
                           ."ORDER BY fd.month,f.updatetime desc) "
                           ."LIMIT ".($page-1)*$size.",$size";
            }
            $data = $this->execute($sql_ret);
//           $data[10]["id"] = 1171;
//           $data[10]["catid"] = 151;
//           $data[10]["tag"] = "试试";
//           $data[10]["thumb"] = "http://oss.aliyuncs.com/qiwo-test/57023655a7081b8b3ab.jpg.thumb.300x300.jpg";
//           $data[10]["description"] = "的厂家都会";
//           $data[10]["type"] =1;
//           $data[10]["index"] = 1;
            $index = $this->get_index_by_month($month);
            foreach ($data as $key => $value) {
                $data[$key]["tag"] = preg_split("/\s*(,|，)\s*/", $value["tag"]);
                if($value["catid"] == 24){
                    $data[$key]["type_id"] = $this->get_type_id_by_content_id($value["id"]); 
                }else{
                    $data[$key]["type"] = $this->get_foodType_by_content_id($value["id"]);
                    $data[$key]["index"] = $index;
                }
            }
         }
         
         return $data;        
    }
    
   
    
        public function add_user_key($uid,$key,$limit=15){
            $time = time();        
            $sql_exist = "select id from fn_user_key where uid = $uid order by updatetime";
            $id_arr = $this->get_id_str($sql_exist);
            if($id_arr["count"] > $limit){
                $sql_remain = "select id from fn_user_key where uid = $uid order by updatetime desc limit 0,". $limit;
                $id_remain = $this->get_id_str($sql_remain);
                $id_remian_str = $id_remain["id_str"];
                if($id_remian_str){
                    $sql_delete = "delete from fn_user_key where uid = $uid and id not in ($id_remian_str)";                   
                    $this->execute($sql_delete);
                }
            }
            
            $sql_check = "select id from fn_user_key where uid = $uid and keywords = '".$key."'";
            $check = $this->execute($sql_check);
            if(!$check){
            $sql_insert = "insert into fn_user_key(uid,keywords,time,updatetime) values($uid,'"."$key"."',$time,$time)";  
            }else{
                $sql_insert = "update fn_user_key set updatetime = ".$time." where id = ".$check[0]["id"] ;
            }
            return $this->execute($sql_insert);
        }
        
        public function get_user_key($uid,$limit=12){
            $sql = "select keywords from fn_user_key where uid = $uid order by updatetime desc limit 0,".$limit;
            return $this->execute($sql);
        }
    
    
     public function get_id_str($sql,$field="id"){
        $ret = array("count"=>0,"id_str"=>"");
        $mix = $this->execute($sql);
        $ret["count"] = count($mix);
        
        $id_str = "";
        foreach($mix as $val){
            $id_str .= $val[$field].",";
        }        
        if($id_str){
            $id_str = substr($id_str, 0,-1);
        }
        $ret["id_str"] = $id_str;
        
        return $ret;
    }  
    
    protected function get_type_id_by_content_id($content_id){
        $sql = "SELECT type_id from fn_mix_type where content_id=".$content_id;
        $data = $this->execute($sql);
        $type_id = isset($data[0]["type_id"])?$data[0]["type_id"]:1;
        return $type_id;
    }
    protected function get_foodType_by_content_id($content_id){
        $sql = "SELECT foodType from fn_content_1_food where id=".$content_id;
        $data = $this->execute($sql);
        $foodType = 1;
        if(isset($data[0]["foodType"])){
            $strType = $data[0]["foodType"];
            for($i=1; $i<=4; $i++){        
                if( false !== strpos($strType, 's:1:"'.$i.'"')){
                    $foodType = $i;
                    break;
                }
            }
        }
        return $foodType;
    }
    
    protected function get_index_by_month($month){   
        $index = 0;
        if( $month <=12 ){
            $index = 0;
        }else if($month >12 && $month <=36 )
        {
            $index = 1;
        }else{
            $index =2;
        }
        
        return $index;;   
    }
}