<?php

class Content_1_topicModel extends Model {
    protected $catid;
    public function __construct(){
        parent::__construct();
        $api_list = \Config::get('api_list');
        $this->catid = $api_list['category']['topic'];
    }

    public function get_primary_key() {
        return $this->primary_key = 'id';
    }

    public function get_fields() {
        return $this->get_table_fields();
    }

    public function get_all($map,$page,$size){
        $map and $where[] = $map;
        $where[] = "c.catid=".$this->catid;
        $where and $where = "where ".join(' and ',$where);
        $sql_count = "select count(1) count from fn_content_1 c LEFT JOIN fn_content_1_topic ct ON c.id=ct.id $where" ;
        $res_count = $this->execute($sql_count);
        $sql = "SELECT c.id,title,thumb FROM fn_content_1 c LEFT JOIN fn_content_1_topic ct ON c.id=ct.id $where ORDER BY c.id DESC LIMIT ".($page-1)*$size.",$size";
//        var_dump($sql);
        $res = $this->execute($sql);
        $ret = array(
            'count'=>$res_count[0]['count'],
            'page'=>$page,
            'size'=>$size,
            'data'=>$res
        );
        return $ret;
    }

    public function get_one($id){
        $sql_1 = "SELECT title,thumb,content,relation from fn_content_1 c left join fn_content_1_topic ct ON c.id=ct.id LEFT JOIN fn_content_1_extend ce ON c.id=ce.id where c.id=$id";
        $res = $this->execute($sql_1);
        if($res[0]['relation']){
            $sql_2 = "SELECT id,title,thumb from fn_content_1 WHERE id in({$res[0]['relation']})";
            $res[0]['supply'] = $this->execute($sql_2);
        }
        return $res;
    }

}