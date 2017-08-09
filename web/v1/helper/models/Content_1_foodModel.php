<?php

class Content_1_foodModel extends Model {

    public function get_primary_key() {
        return $this->primary_key = 'id';
    }

    public function get_fields() {
        return $this->get_table_fields();
    }
    
    public function get_all_data(){
        $ret = array();
        for($i=0; $i<3 ;$i ++){
            switch ($i) {
                case 0:
                    $month = 0;
                    break;
                case 1:
                    $month = 36;
                    break;
                case 2:
                    $month = 37;
                default:
                    break;
            }
        $data1 = $this->get_rand_data_by_foodType(1,$month);
        $data2 = $this->get_rand_data_by_foodType(2,$month);
        $data3 = $this->get_rand_data_by_foodType(3,$month);
        $data4 = $this->get_rand_data_by_foodType(4,$month); 
        $ret[$i] = array(0=>$data1,1=>$data2,2=>$data3,3=>$data4);
        unset($data1,$data2,$data3,$data4);
        }        
        return $ret;        
    }
    
    public function get_rand_data_by_foodType($type,$month=0,$count=3){
         //a .先查询满足条件的 b.随机挑出3条
        $data = "";
        $id_str = "";
        $sql_month = $this->get_sql_month($month);  
        $id_sql = "SELECT id from fn_content_1_food WHERE month ".$sql_month." AND foodType LIKE '%s:1:\"$type\"%'";
        $id_arr = $this->execute($id_sql);   
        $id_arr = $this->get_rand_content_id($id_arr);    
         foreach ($id_arr as $value) {
                $id_str .= $value["id"].",";
            }  
        
        if($id_str){
            $id_str = substr($id_str, 0, -1);
            $sql = "SELECT fd.id,f.title,f.thumb,f.description, fd.month, fd.foodType ". 
                      "FROM  fn_content_1_food fd LEFT JOIN fn_content_1 f ON fd.id = f.id  ".
                      "WHERE fd.id in ($id_str) and fd. month ".$sql_month." AND fd.foodType LIKE '%s:1:\"$type\"%'";
            $data = $this->execute($sql);        
        }
        return $data;
    }

    
    
    
    public function get_data_by_foodType($type,$month=0,$read_id="",$page=1,$size=3){    
        $where = $type ? "foodType LIKE '%s:1:\"$type\"%'" :'1=1';
        $where .= $read_id? " and id not in ($read_id)":"";
        $sql_month = $this->get_sql_month($month);
        $sql_content_id = "SELECT distinct(id) FROM fn_content_1_food WHERE $where and month $sql_month";
        $res_id = $this->execute($sql_content_id);   
        
        if($res_id){
            foreach ($res_id as $id) {            
                $id_str .= intval($id["id"]).",";
            }
            $id_str = substr($id_str, 0, -1);       
        }
//        1. 按month分小于12个月month<=12，1-3岁month=36，3岁以后month=37
//        2. 查询数据要按食物归类s:1:"1"，s:1:"2"，s:1:"3"，s:1:"4"   
        $sql = "SELECT fd.id,f.title,f.thumb,f.description, fd. month, fd.foodType ". 
                  "FROM  fn_content_1_food fd LEFT JOIN fn_content_1 f ON fd.id = f.id  ".
                  "WHERE fd.id in ($id_str) ".
                  "ORDER BY fd. month,f.updatetime desc ".
                  "LIMIT ".($page-1)*$size.",$size";
        $data = $this->execute($sql);        
        return $data;
    }    
    
    
     public function get_sql_month($month){
         $sql_month = "is not null";       
        if( $month <=12){
            $sql_month = "between $month and 12";
        }else if( $month <= 36 && $month > 12){
            $sql_month = "=36";
        }else if($month > 36){
            $sql_month = "=37";
        }
        return $sql_month;
    }
        
     public function get_by_id($id){
        $sql = "SELECT fcd.id,fcd.content,fc.thumb,fc.title  FROM fn_content_1_food fcd left join fn_content_1 fc on fcd.id = fc.id WHERE fcd.id = ".$id;                 
        $data = $this->execute($sql);
        return $data;     
    }
    
    public function get_title_by_food_type($type){
        $title = "营养膳食";
        switch ($type) {
            case 1:
                $title = "时令水果";                
                break;
            case 2:
                $title = "肉禽蛋奶";
                break;
            case 3:
                $title = "五谷杂粮";
                break;
            case 4:
                $title = "蔬菜菌类";
                break;
            default:
                break;
        }
        return $title;
    }
    protected function get_rand_content_id($id_arr,$count=3){
        $result_id = $id_arr;
        $total = count($id_arr);
        if( $total > $count ){
            $tmp_index = array();
            for($i=0; $i<$total; $i++){
                $tmp_index[] = $i;
            }
            $tmp_id = array_rand($tmp_index, $count);  
            unset($result_id);           
            foreach ($tmp_id as $val){
                $result_id[] = $id_arr[$val];
            }
        }
          
        return $result_id;
    }
}