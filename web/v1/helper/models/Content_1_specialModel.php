<?php

class Content_1_specialModel extends Model {

    protected $catid;
    public function __construct(){
        parent::__construct();
        $api_list = \Config::get('api_list');
        $this->catid = $api_list['category']['special'];
    }

    public function get_primary_key() {
        return $this->primary_key = 'id';
    }

    public function get_fields() {
        return $this->get_table_fields();
    }

    public function get_all($page,$size){
        $sql_count = "select count(1) from fn_content_1 where catid=".$this->catid;
        $res_count = $this->execute($sql_count);
        $sql = "SELECT id,title,thumb FROM fn_content_1 WHERE catid=".$this->catid." ORDER BY id desc LIMIT ".($page-1)*$size.",$size";
        $res = $this->execute($sql);
        $ret = array(
            'count'=>$res_count[0]['count'],
            'page'=>$page,
            'size'=>$size,
            'data'=>$res
        );
        return $ret;
    }

    public function get_one($id,$user_id=0){

        $user_id = $user_id ? $user_id :0;
        $sql_1 = "SELECT title,thumb,content,relation from fn_content_1 c left join fn_content_1_special cs ON c.id=cs.id LEFT JOIN fn_content_1_extend ce ON c.id=ce.id where c.id=$id";
        $res = $this->execute($sql_1);
        if($res[0]['relation']){
            $sql_2 = "SELECT c.id,title,thumb,content from fn_content_1 c left join fn_content_1_supply cs ON c.id=cs.id WHERE c.id in({$res[0]['relation']})";
            $res_2 = $this->execute($sql_2);
            if($res_2){
                $sql_3 = "select content_id from fn_my_supply where userid=$user_id and content_id in ({$res[0]['relation']})";//查询已收藏用品
                $res_3 = $this->execute($sql_3);
                if($res_3){
                    foreach($res_3 as $vv){
                        $cid_3[]=$vv['content_id'];//已收藏用品
                    }
                    foreach($res_2 as &$vvv){
                        in_array($vvv['id'],$cid_3) and $vvv['pick'] = 1;
                    }
                    unset($vvv);
                }
                $res[0]['supply'] = $res_2;
            }
        }
        return $res;
    }

}