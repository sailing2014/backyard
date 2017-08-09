<?php

class TagsModel extends Model
{
    protected $cache;
    public function __construct() {
        parent::__construct();
        $this->cache = new cache_file();
    }
    public function get_primary_key()
    {
        return $this->primary_key = 'id';
    }

    public function get_fields()
    {
        return $this->get_table_fields();
    }

    /**
     *
     */
    public function count($where="",$field=1){
        $where && $where = "where $where";
        $sql = "select count($field) count from fn_tags $where";
        $res = $this->execute($sql);
        return (int)$res[0]['count'];
    }

    /**
     * 获取分页标签，根据点击排序
     * @param string $where
     * @param int $page
     * @param int $size
     * @return array
     */
    public function get_all($where,$page=1,$size=10){
        $where = $where ? 'WHERE '.join(' AND ',$where) : '';
        $sql_count = "SELECT count(1) count FROM fn_tags $where";
        $res_count = $this->execute($sql_count);
        $return['count']= (int)$res_count[0]['count'];
        $sql = "SELECT * FROM fn_tags $where ORDER BY rank desc,id desc LIMIT ".($page-1)*$size.",".$size;
        $return['data'] =$this->execute($sql);
        return $return;
    }

    /**
     * 获取单条记录
     * @param int $id
     * @return array
     */
    public function get_one($id){
        return $this->getOne("id=$id");
    }

    /**
     * 设置单条记录
     * @param int $id
     * @param array $data
     * @return boolean
     */
    public function set_one($id,$data){
        if($id){
            $return = $this->update($data,"id=$id");
        }else{
            $return = $this->insert($data);
        }
        return $return;
    }

    /**
     * 批量设置
     * @param array $set
     * @param array $ids
     * @return boolean
     */
    public function set_all($set,$ids){
        $result = $this->update($set,"id in($ids)");
        return $result;
    }

    /**
     * 获取分页标签，根据点击排序
     * @param int $page
     * @param int $size
     * @return array
     */
    public function get_tags($tags,$page=1,$size=10){
        $where = $tags ? "WHERE name like '%$tags%'" : "";
        $sql_count = "SELECT count(id) count FROM fn_tags $where";
        $res_count = $this->execute($sql_count);
        $return['count']= (int)$res_count[0]['count'];
        $sql = "SELECT * FROM fn_tags $where ORDER BY rank desc,hits desc LIMIT ".($page-1)*$size.",$size";
        //echo $sql;
        $return['data'] =$this->execute($sql);
        return $return;
    }

    /**
     * 获取文章标签
     * @param int $content_id
     * @return array
     */
    public function get_tags_by_content_id($content_id){
        $sql = "SELECT t.* FROM `fn_tags` t LEFT JOIN `fn_tags_content` tc ON t.id=tc.tags_id WHERE tc.content_id=".$content_id;
        return $this->execute($sql);
    }

    /**
     * 设置文章标签（本文是否存在此标签，系统是否存在此标签）
     * @param int $content_id
     * @param string $tags
     * @return array
     */
    public function set_tags_by_content_id($content_id,$tags,$mode){
        $tags = str_replace("，",",",$tags);
        $new = explode(',',$tags);//新标签
        //去除空数据
        foreach($new as $key => $val){
            if(trim($val)==''){
                unset($new[$key]);
            }
        }
//        echo "new";        dump($new);
        if($mode) {//编辑模式，先删后增

//            $res = $this->from(array('a'=>'tags','b'=>'tags_content'))
//                        ->where("a.id=b.tags_id")
//                        ->where("b.content_id=$content_id")
//                        ->select();
            //$res = $this->from('tags')->join('tags_content','fn_tags.id = fn_tags_content.tags_id')->where("fn_tags_content.content_id=".$content_id)->select();
            //dump($res);
            $sql_old = "SELECT t.id,t.name FROM fn_tags t LEFT JOIN fn_tags_content tc ON t.id=tc.tags_id WHERE tc.content_id=".$content_id;
//            dump($sql_old);
            $res = $this->execute($sql_old);

//            echo "res";            dump($res);
            if(!empty($res)){//本文已存在标签
                foreach ($res as $k => $v) {
                    $old[$v['id']] = $v['name'];
                }
//                echo "old";                dump($old);
                $add = array_diff($new, $old);
                $del = array_diff($old, $new);
            }else{
                $add = $new;
            }
            //批量删除
            if($del){
                $ids = join(',',array_keys($del));
//                $sql_del_1 = "delete from fn_tags where id in($ids)";//删除tags
//                $this->execute($sql_del_1);
                $sql_del_2 = "delete from fn_tags_content where content_id =$content_id and tags_id in($ids)";//删除tags_content
                $this->execute($sql_del_2);
            }

        }else{
            $add = $new;
        }
//        echo "add";        dump($add);
//        echo "del";        dump($del);
        //批量添加tags
        if($add){
            foreach($add as $k=>$v){
                //$adds[] = "($v)";
                $sql_get_0 = "select id from fn_tags where name='$v'";
                $res_get = $this->execute($sql_get_0,false);
                if(!($tags_id = $res_get['id'])){
                    $sql_add_1 = "insert into fn_tags(name) VALUE ('$v')";//
                    $this->execute($sql_add_1);
                    $tags_id = $this->get_insert_id();
                }
                $sql_add_2 = "insert into fn_tags_content(tags_id,content_id) VALUE ($tags_id,$content_id)";
                $this->execute($sql_add_2);
            }
            //$sql = "insert into fn_tags(name) VALUES ".join(',',$adds);
            //$res_add = $this->execute($sql);
        }
        return true;
    }

    /**
     * 批量删除标签
     */
    public function del_tags_content_id($content_id){
        $sql_del_1 = "delete from fn_tags where id in( select tags_id from fn_tags_content where content_id=$content_id)";
        $res_1 = $this->execute($sql_del_1);
        $sql_del_2 = "delete from fn_tags_content where content_id=$content_id";
        $res_2 = $this->execute($sql_del_2);
    }

    /**
     * 根据标签id获取文章分页
     * @param int $tags_id
     * @param int $page
     * @param int $size
     * @param int $month
     * @return array
     */
   public function get_content_by_tags_id($tags_id,$page=1,$size=10,$month=-1){
        $sql_count = "select count(c.id) count from fn_tags_content tc left join fn_content_1 c on tc.content_id=c.id where tc.tags_id=$tags_id";
        $res_count = $this->execute($sql_count);
        $return['count'] = (int)$res_count[0]['count'];
        //$sql = "select c.id, c.title, c.thumb, c.hits+c.lies as hits from fn_tags_content tc left join fn_content_1 c on tc.content_id=c.id ".
        //        "where c.catid=24 and tc.tags_id=$tags_id ORDER BY c.listorder desc, c.hits desc, c.id desc LIMIT ".($page-1)*$size.",$size";
        if( $month < 0 ){
        $sql = "select c.id, c.title, c.thumb,c.inputtime, c.hits+c.lies as hits from fn_tags_content tc left join fn_content_1 c on tc.content_id=c.id ".
            "where tc.tags_id=$tags_id ORDER BY c.id desc LIMIT ".($page-1)*$size.",$size";
        }else{             
            $order_str = $this->getOrderStr($month);
            $sql = "select c.id, c.title, c.thumb,c.inputtime, c.hits+c.lies as hits from fn_tags_content tc left join fn_content_1 c on tc.content_id=c.id ".
                    "right join fn_content_1_mix cm on  c.id = cm.id ".
                    "where tc.tags_id=$tags_id  and ( $month between cm.start and cm.close) ".
                    "ORDER BY FIND_IN_SET(`start`, '$order_str'),c.id desc ".
                    "LIMIT ".($page-1)*$size.",$size";
        }        
        $return['data'] = $this->execute($sql);       
        $apilist =\Config::get('api_list');
        foreach($return['data'] as $k=>$v){
            $return['data'][$k]['url']= $apilist['content']['url'].$v['id'];
        }
        //var_dump($return);
        $this->auto_inc($tags_id);//访问自增

        return $return;
    }

    /**
     * 根据关键字获取文章
     * @param string $tags
     * @param int $page
     * @param int $size
     * @return array
     */
    public function get_content_by_tags($tags,$page,$size){
        $sql_count = "select count(c.id) count from fn_content_1 c WHERE id IN(".
            "SELECT DISTINCT(tc.content_id) FROM fn_tags_content tc LEFT JOIN fn_tags t ON tc.tags_id=t.id WHERE t.name='$tags')";
        $res_count= $this->execute($sql_count);
        $return['count'] =(int)$res_count[0]['count'];
        $sql = "SELECT c.id,c.title,c.thumb,c.inputtime, c.hits+c.lies as hits from fn_content_1 c WHERE id IN(".
            "SELECT DISTINCT(tc.content_id) FROM fn_tags_content tc LEFT JOIN fn_tags t ON tc.tags_id=t.id WHERE t.name='$tags'".
            ") ORDER BY listorder desc,hits desc LIMIT ".($page-1)*$size.",$size";
//        var_dump($sql);
        $return['data'] = $this->execute($sql);
        $apilist =\Config::get('api_list');
        foreach($return['data'] as $k=>$v){
            $return['data'][$k]['url']= $apilist['content']['url'].$v['id'];
        }
        return $return;
    }

    /**
     * @param $tags_id
     * @return array
     */
    public function get_hits_of_tags($tags_id){
        //$sql = "select hits from fn_tags where id=$tags_id";
        $res = $this->getOne('id=?',$tags_id,'hits');
        return $res['hits'];
    }
    /**
     * @param $tags_id
     * @return array
     */
    public function set_hits_of_tags($tags_id,$hits){
        //$sql = "update fn_tags set hits = $hits where id=$tags_id";
        $res = $this->update(array('hits'=>$hits),array('id'=>$tags_id));
        return $res;
    }

    /**
     * 访问量自增
     * @param int $tags_id
     * @return array
     */
    public function auto_inc($tags_id,$auto = 1){
        $cache_id = sprintf('api_tags_hits_%s',$tags_id);
        $cache = $this->cache->get($cache_id);

        $time=time();
        if(empty($cache)){
            $hits = $this->get_hits_of_tags($tags_id);
            $cache = array(
                'time'=>$time,
                'hits'=>$hits,
                'auto'=>1);
        }else{
            $cache = array(
                'time'=>$time,
                'hits'=>$cache['hits'],
                'auto'=>$cache['auto']+1);
        }
        $hits =$cache['hits'] + $cache['auto'];
        if(empty($auto)){
            return $hits;
        }
        //自增
        $this->cache->set($cache_id,$cache);
        if($cache['time']-$time>18000 || $cache['auto']> 10){//超过三小时或者自增10次
            $res = $this->set_hits_of_tags($tags_id,$hits);//更新一次数据库
            if($res){
                $cache = array(
                    'time'=>$time,
                    'hits'=>$hits,
                    'auto'=>0);
                $this->cache->set($cache_id,$cache);
            }
        }
        return $hits;
    }

    /**
     * 获取排序字符串
     * 
     * @param int $month 月份
     * @return type
     */
    protected function getOrderStr($month){
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