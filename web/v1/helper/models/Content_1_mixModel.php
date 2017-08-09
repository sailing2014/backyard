<?php

class Content_1_mixModel extends Model {

    public function get_primary_key() {
        return $this->primary_key = 'id';
    }

    public function get_fields() {
        return $this->get_table_fields();
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

    public function get_all_by_type($type_id=0,$field="*",$page=1,$size=10,$month=-1){
        $sql_count = "SELECT count(1) count FROM fn_mix_type WHERE type_id = $type_id";
        $count = $this->execute($sql_count);
        if($month < 0)
        {
        $sql = "SELECT c.id, c.$field FROM fn_content_1 c LEFT JOIN fn_mix_type t ON c.id=t.content_id ".
            "WHERE type_id=$type_id LIMIT ".($page-1)*$size.",$size";
        }else{
            $order_str = $this->getOrderStr($month);
            $sql = "SELECT c.id, c.$field FROM fn_content_1 c LEFT JOIN fn_mix_type t ON c.id=t.content_id ".
                    "right join fn_content_1_mix cm on  c.id = cm.id ".
                    "WHERE type_id=$type_id and ( $month between cm.start and cm.close) ".
                    "ORDER BY FIND_IN_SET(`start`, '$order_str') ".
                    "LIMIT ".($page-1)*$size.",$size";
        }        
        $res = $this->execute($sql);
        return array('data'=>$res,'count'=>$count['count']);
    }

    /**
     * 批量设置类目
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
            $sql_del = "delete from fn_grow_type where type_id in ($del_ids)";
            $this->execute($sql_del);
        }
        if($add){
            foreach($add as $v){
                $data[] = "($v,$id)";
            }
            $sql = "insert into fn_grow_type (`type_id`,`content_id`) VALUES ".join(',',$data);
            $this->execute($sql);
        }

    }

    public function del_any($id){
        $sql_del = "delete from fn_grow_type where content_id in ($id)";
        $res = $this->execute($sql_del);
        return $res;
    }
    
    /**
     * 获取排序字符串
     * 
     * @param int $month 月份
     * @return type
     */
    public function getOrderStr($month){
        if($month < 0) { $month = 0;}
        else if($month > 12) { $month = 12;}
        
        $ret = "";
        for ($i = $month ; $i >= 0; $i--){
            $ret .= "$i,";
        }
        
        $ret = substr($ret, 0,-1);
        return $ret;
    }
}