<?php

class Tags_typeModel extends Model
{
    protected $status;
    protected $cache;
    protected $cache_id;
    public function __construct() {
        parent::__construct();
        $this->status   = array(1=>'启用',0=>'禁用');
        $this->cache    = new cache_file();
        $this->cache_id = sprintf("cache_tags_type");
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
     * 获取所有类型，根据点击排序
     * @param string $where
     * @param int $page
     * @param int $size
     * @return array
     */
    public function  get_all($where,$page=1,$size=10){
        $where = $where ? $where :'1=1';
        $sql_count = "SELECT count('id') count FROM fn_tags_type WHERE $where";
        $res_count = $this->execute($sql_count);
        $return['count']= (int)$res_count[0]['count'];
        $sql = "SELECT * FROM fn_tags_type WHERE $where ORDER BY rank desc,id desc LIMIT ".($page-1)*$size.",".$size;
        $return['data'] =$this->execute($sql);
        return $return;
    }

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
     * 获取缓存
     */
    public function get_cache(){
//        $this->cache->delete($this->cache_id);
        $cache = $this->cache->get($this->cache_id);
        if(empty($cache)){
            $result = $this->get_all('',1,20);
            foreach($result['data'] as $k=>$v){
                $cache[(int)$v['id']]=$v['name'];
            }
            $this->cache->set($this->cache_id,$cache);
        }
        return $cache;
    }

    /**
     *清空缓存
     */
    public function clr_cache(){
        return $this->cache->set($this->cache_id,false);
    }

}