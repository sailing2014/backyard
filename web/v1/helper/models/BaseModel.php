<?php

class BaseModel extends Model {

    public  function  init(){

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
     * 根据条件统计
     * @param string $where
     * @param string $field
     * @return array
     */
    public function count($where="",$field=1){
        $where && $where = "where $where";
        $sql = "select count($field) count from ".$this->getTableName()." $where";
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
        $sql_count = "SELECT count(1) count FROM ".$this->getTableName()." $where";
        $res_count = $this->execute($sql_count);
        $return['count']= (int)$res_count[0]['count'];
        $sql = "SELECT * FROM ".$this->getTableName()." $where ORDER BY rank desc,id desc LIMIT ".($page-1)*$size.",".$size;
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
     * 批量删除
     * @param string $ids
     * @param string $where
     * @return array
     */
    public function del_any($ids,$where){

    }
}