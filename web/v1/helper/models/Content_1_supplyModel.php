<?php

class Content_1_supplyModel extends Model {

    public function get_primary_key() {
        return $this->primary_key = 'id';
    }

    public function get_fields() {
        return $this->get_table_fields();
    }

    public function get_list_by_month($user_id=0,$type_id=0,$start=0,$close=0,$page=1,$size=10){
        $type_id and $where[] = "cs.catid=$type_id";
        if($start!=0 || $close!=0){
            $where[] ="start<=$close AND close>=$start";
        }
        $where and $where ="where ".join(' and ',$where);
        $sql_count = "SELECT count(1) FROM fn_content_1_supply cs $where ";
        $res_count = $this->execute($sql_count);

        $limit = "LIMIT ".($page-1)*$size.",$size";
        $sql1 = "select c.id,c.title,c.thumb,cs.content,cs.start,cs.close from fn_content_1 c left JOIN fn_content_1_supply cs ON c.id=cs.id $where $limit";//条件查询
//        var_dump($sql1);
        $res1 = $this->execute($sql1);
        if($res1){
            foreach($res1 as $v){
                $cid_1[] = $v['id'];//当前页用品
            }
            $cid_1 = join(',',$cid_1);
            $sql2 = "select content_id from fn_my_supply where userid=$user_id and content_id in ($cid_1)";//查询已收藏用品
            $res2 = $this->execute($sql2);
            if($res2){
                foreach($res2 as $vv){
                    $cid_2[]=$vv['content_id'];//已收藏用品
                }
                foreach($res1 as &$vvv){
                    in_array($vvv['id'],$cid_2) and $vvv['pick'] = 1;
                }
                unset($vvv);
            }
        }
        return array(
            'count'=>$res_count['count'],
            'page'=>$page,
            'size'=>$size,
            'data'=>$res1
        );
    }

    public function pick($userid=0,$content_id=0){
        $sql_count = "select count(1) as count from fn_my_supply WHERE userid=$userid AND content_id=$content_id";
        $res_count = $this->execute($sql_count);
//        var_dump($res_count[0]['count']);
        if($res_count[0]['count']==0){
            $sql = "insert into fn_my_supply (`userid`,`content_id`) VALUES ($userid, $content_id)";
            $res = $this->execute($sql);
        }else{
            $res = -1;
        }
        return $res;
    }

    public function get_pick($userid=0, $page=1, $size=10){
        $sql_count = "select count(1) count from fn_my_supply where userid = $userid";
        $res_count = $this->execute($sql_count);
        $sql1 = "select c.id,c.title,c.thumb,cs.content from fn_content_1 c left join fn_my_supply ms on c.id=ms.content_id left JOIN fn_content_1_supply cs ON c.id=cs.id where ms.userid=$userid limit ".($page-1)*$size.",$size";//条件查询
//        var_dump($sql1);
        $res = $this->execute($sql1);
        $ret = array(
            'count' => $res_count[0]['count'],
            'page'=>$page,
            'size'=>$size,
            'data'=>$res
        );
        return $ret;
    }
    
    public function get_special_by_title($title){
        $ret = array();
        $sql = "select id,catid from fn_content_1 where title= '". $title . "'";
        $res= $this->execute($sql);
        $id = isset($res[0]['id'])?$res[0]['id']:0;
        if($id){
            $sql1 = "select id,catid,content from fn_content_1_special where id= $id";
            $res1 = $this->execute($sql1);     
            $ret["id"] = $res1[0]["id"];
            $ret["catid"] = $res1[0]["catid"];
            $ret["content"] = $res1[0]["content"];
        }
        return $ret;
    }
}