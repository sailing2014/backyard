<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/7/13
 * Time: 16:56
 */

class Mix_typeModel extends Model {
    public  function __construct(){
        parent::__construct();
    }

    /**
     * @param string $field
     * @param null $where
     * @return mixed
     */
    public function get_all($field='*',$where="1=1"){
        $res = $this->getAll($where,"",$field);
        return $res;
    }

    /**
     * 单篇文章设置类型
     * @param int $id
     * @param array $types_new
     */
    public function set_any($id,$types_new){
        $types_exist = $this->get_all('type_id','content_id='.$id);
        if($types_exist){
            foreach($types_exist as $v){
                $types_old[] = $v['type_id'];
            }
        }

        if($types_old){
            //删除
            $del = array_diff($types_old,$types_new);

            //添加
            $add = array_diff($types_new,$types_old);
        }else{
            $add = $types_new;
        }

        if($del){
            $del_ids = join(',',$del);
            $sql_del = "delete from fn_mix_type where type_id in ($del_ids) and content_id in ($id)";
            $this->execute($sql_del);
        }
        if($add){
            foreach($add as $v){
                $data[] = "($v,$id)";
            }
            $sql = "insert into fn_mix_type (`type_id`,`content_id`) VALUES ".join(',',$data);
            $this->execute($sql);
        }

    }

    public function del_any($id){
        $sql_del = "delete from fn_mix_type where content_id in ($id)";
        $res = $this->execute($sql_del);
        return $res;
    }
    
    public function get_content_list_by_type_id($type_id,$month=0,$read_id="",$page=1,$size=10){
        $where = $type_id ? "type_id=$type_id" :'1=1';
        $where .= $read_id? " and content_id not in ($read_id)":"";
        $sql_content_id = "SELECT distinct(content_id) FROM fn_mix_type WHERE $where";
        $res_id = $this->execute($sql_content_id);        
        $return['count']= count($res_id);
        
        if($res_id){
            foreach ($res_id as $id) {            
                $id_str .= intval($id["content_id"]).",";
            }
            $id_str = substr($id_str, 0, -1);
            $sql1 = "SELECT fcm.id,fcm.tag,fc.title,fc.thumb,fc.description FROM fn_content_1_mix fcm left join fn_content_1 fc on fcm.id = fc.id WHERE fcm.id in ($id_str) and (fcm.start<=$month or fcm.close>=$month) ORDER BY fc.updatetime desc LIMIT ".($page-1)*$size.",".$size;       
            $data1 = $this->execute($sql1);
            $sql2 = "SELECT fcm.id,fcm.tag,fc.title,fc.thumb,fc.description FROM fn_content_1_mix fcm left join fn_content_1 fc on fcm.id = fc.id WHERE fcm.id in ($id_str) and (fcm.start> $month and fcm.close<$month) ORDER BY fc.updatetime desc LIMIT ".($page-1)*$size.",".$size;                          
            $data2 = $this->execute($sql2);    
            $return['data'] = array_merge($data1,$data2);
        }else{
            $return['data'] = array();
        }       
        return $return;
    }
    
    public function get_by_id($id){
        $sql = "SELECT fcm.id,fcm.tag,fc.title,fcm.content  FROM fn_content_1_mix fcm left join fn_content_1 fc on fcm.id = fc.id WHERE fcm.id = ".$id; /* LIMIT ".($page-1)*$size.",".$size; */                  
        $data = $this->execute($sql);
        return $data;     
    }
    
    public function get_list_by_tag($tag,$month,$page=1,$size=10){
        $sql1 = "SELECT fcm.id,fcm.tag,fc.title,fc.thumb,fc.description FROM fn_content_1_mix fcm left join fn_content_1 fc on fcm.id = fc.id WHERE fcm.tag like '%".$tag."%' and (fcm.start<=$month or fcm.close>=$month) ORDER BY fcm.id desc LIMIT ".($page-1)*$size.",".$size;
        $data1 = $this->execute($sql1);
        $sql2 = "SELECT fcm.id,fcm.tag,fc.title,fc.thumb,fc.description FROM fn_content_1_mix fcm left join fn_content_1 fc on fcm.id = fc.id WHERE fcm.tag like '%".$tag."%' and (fcm.start> $month and fcm.close<$month) ORDER BY fc.updatetime desc LIMIT ".($page-1)*$size.",".$size;                          
        $data2 = $this->execute($sql2);    
        $data = array_merge($data1,$data2);  
        return $data;
    }
   
}