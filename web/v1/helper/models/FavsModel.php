<?php

class FavsModel extends Model
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
     * 获取分页收藏
     * @param string $where
     * @param int $page
     * @param int $size
     * @return array
     */
    public function get_all($where,$page=1,$size=10){
        foreach($where as $k=>$v){
            $temp[]=" $k = '$v' ";
        }
        $where = $temp ? 'WHERE '.join(' AND ',$temp) : '';
        $sql_count = "SELECT count('id') count FROM fn_favs $where";
        $res_count = $this->execute($sql_count);
        $return['count']= (int)$res_count[0]['count'];
        $sql = "SELECT c.id,c.title,c.thumb,c.description,c.url,c.hits+c.lies as hits FROM fn_favs f left join fn_content_1 c on f.content_id=c.id  $where ORDER BY f.id desc LIMIT ".($page-1)*$size.",".$size;
        //var_dump($sql);
        $return['data'] =$this->execute($sql);
        return $return;
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
     * 一篇文章被收藏多少次
     * @param int $content_id
     * @return int
     */
    public function get_count_content($content_id){
        //$sql = "select count('user_id') from fn_favs where content_id=$content_id";
        return $this->count('favs','user_id',"content_id=$content_id");
    }

    /**
     * 一个用户收藏了多少文章
     * @param int $user_id
     * @return int
     */
    public function get_count_user($user_id){
        return $this->count('favs','content_id',"content_id=$content_id");
    }

    /**
     * @param $user_id
     * @return bool|resource
     */
    public function del_any($user_id,$content_ids){
        return $this->delete("user_id=$user_id and content_id in($content_ids)");
    }

}