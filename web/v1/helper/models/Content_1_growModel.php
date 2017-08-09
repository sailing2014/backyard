<?php

class Content_1_growModel extends Model {

    public function get_primary_key() {
        return $this->primary_key = 'id';
    }

    public function get_fields() {
        return $this->get_table_fields();
    }
 
    public function get_title(){
        $sql = "select id,title from fn_content_1 where catid=20 order by listorder asc limit 0,52";
        $ret = $this->execute($sql);
        return $ret;
    }
    
    public function get_content($id=0){        
        $where = "";
        $id and $where = "where id=".$id;
        
        $sql = "select id,content from fn_content_1_grow ".$where;
        $ret = $this->execute($sql);
        return $ret;
    }    
}