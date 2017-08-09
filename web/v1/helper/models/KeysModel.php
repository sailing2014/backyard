<?php

class KeysModel extends Model {

    public function get_primary_key() {
        return $this->primary_key = 'id';
    }

    public function get_fields() {
        return $this->get_table_fields();
    }
	
	public function getList($num, $cache) {
		return $this->order('listorder DESC,id DESC')->limit($num)->select(true, $cache);
	}
	
	public function getData($kw) {
		return $this->where('letter=?', $kw)->select();
	}
	
	public function listData($keys, $where, $cache) {
		$data = $this->from('keys_cache')->where('`keys`=?', $keys)->select(false);
		$data = empty($data) ? $this->addData($keys, $where) : $data;
		if (empty($data)) return false;
	    return time() - $data['addtime'] > $cache ? $this->updateData($data['id'], $where) : $data;
	}
	
	private function addData($keys, $where) {
	    $data = $this->execute('SELECT id FROM ' . $this->prefix . 'content_' . App::get_site_id() . ' WHERE ' . $where, true);
		if (empty($data)) return false;
		$total= count($data);
		$ids  = '';
		foreach ($data as $t) { $ids .= $t['id'] . ','; }
		$ids  = substr($ids, -1) == ',' ? substr($ids, 0, -1) : $ids;
		$data = array(
		    'params'  => md5($where),
			'addtime' => time(),
			'total'   => $total,
			'keys'     => $keys,
			'sql'     => 'SELECT * FROM ' . $this->prefix . 'content_' . App::get_site_id() . ' WHERE `id` IN (' . $ids . ') ORDER BY `updatetime` DESC',
		);
		$this->set_table_name('keys_cache');
		return $this->insert($data) ? $data : false;
	}
	
	private function updateData($id, $where) {
	    $data = $this->execute('SELECT id FROM ' . $this->prefix . 'content_' . App::get_site_id() . ' WHERE ' . $where, true);
		$this->set_table_name('keys_cache');
		if (empty($data)) {
		    $this->delete('id=' . $id);
		    return false;
		}
		$total= count($data);
		$ids  = '';
		foreach ($data as $t) {  $ids .= $t['id'] . ','; }
		$ids  = substr($ids, -1) == ',' ? substr($ids, 0, -1) : $ids;
		$data = array(
			'addtime' => time(),
			'total'   => $total,
			'sql'     => 'SELECT * FROM ' . $this->prefix . 'content_' . App::get_site_id() . ' WHERE `id` IN (' . $ids . ') ORDER BY `updatetime` DESC',
		);
		$this->update($data, 'id=' . $id);
		return $data;
	}


    /**
     * 根据关键字获取文章
     * @param string $keys
     * @param int $page
     * @param int $size
     * @return array
     */
    public function get_content_by_keys($keys,$page,$size){
        $sql_count = "select count(c.id) count from fn_content_1 c WHERE id IN(".
            "SELECT DISTINCT(tc.content_id) FROM fn_keys_content tc LEFT JOIN fn_keys t ON tc.keys_id=t.id WHERE t.name='$keys')";
        $res_count= $this->execute($sql_count);
        $return['count'] =(int)$res_count[0]['count'];
        $sql = "SELECT c.id,c.title,c.thumb, c.hits+c.lies as hits from fn_content_1 c WHERE id IN(".
            "SELECT DISTINCT(tc.content_id) FROM fn_keys_content tc LEFT JOIN fn_keys t ON tc.keys_id=t.id WHERE t.name='$keys'".
            ") ORDER BY listorder desc,hits desc LIMIT ".($page-1)*$size.",$size";
//        var_dump($sql);
        $return['data'] = $this->execute($sql);
        foreach($return['data'] as $k=>$v){
            $return['data'][$k]['url']= "http://qbb.qiwocloud1.com/v1/helper/content/content.php?id=".$v['id'];
        }
        return $return;
    }

    /**
     * 获取热门关键字分页，根据点击排序
     * @param int $page
     * @param int $size
     * @return array
     */
    public function get_keys($page=1,$size=10){
        $sql_count = "SELECT count(t.id) count FROM `fn_keys` t LEFT JOIN `fn_keys_content` tc ON t.id=tc.keys_id";
        $res_count = $this->execute($sql_count);
        $return['count']= $res_count[0]['count'];
        $sql = "SELECT DISTINCT(t.id), t.* FROM `fn_keys` t LEFT JOIN `fn_keys_content` tc ON t.id=tc.keys_id ORDER BY t.hits desc,t.id desc LIMIT ".($page-1)*$size.",".$size;
        $return['data'] =$this->execute($sql);
        return $return;
    }

    /**
     * 获取文章关键字
     * @param int $content_id
     * @return array
     */
    public function get_keys_by_content_id($content_id){
        $sql = "SELECT t.* FROM `fn_keys` t LEFT JOIN `fn_keys_content` tc ON t.id=tc.keys_id WHERE tc.content_id=".$content_id;
        return $this->execute($sql);
    }

    /**
     * 批量删除关键词
     */
    public function del_keys_content_id($content_id){
        $sql_del_1 = "delete from fn_keys where id in( select keys_id from fn_keys_content where content_id=$content_id)";
        $res_1 = $this->execute($sql_del_1);
        $sql_del_2 = "delete from fn_keys_content where content_id=$content_id";
        $res_2 = $this->execute($sql_del_2);
    }

    /**
     * @param $keys_id
     * @return array
     */
    public function get_hits_of_keys($keys_id){
        //$sql = "select hits from fn_keys where id=$keys_id";
        $res = $this->getOne('id=?',$keys_id,'hits');
        return $res['hits'];
    }
    /**
     * @param $keys_id
     * @return array
     */
    public function set_hits_of_keys($keys_id,$hits){
        //$sql = "update fn_keys set hits = $hits where id=$keys_id";
        $res = $this->update(array('hits'=>$hits),array('id'=>$keys_id));
        return $res;
    }

    /**
     * 访问量自增
     * @param int $keys_id
     * @return array
     */
    public function auto_inc($keys_id,$auto = 1){
        $cache_id = sprintf('api_keys_hits_%s',$keys_id);
        $cache = $this->cache->get($cache_id);

        $time=time();
        if(empty($cache)){
            $hits = $this->get_hits_of_keys($keys_id);
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
            $res = $this->set_hits_of_keys($keys_id,$hits);//更新一次数据库
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

}