<?php
class Ke_colorModel extends Model{
    public function get_primary_key() {
        return $this->primary_key = 'id';
    }


    public function get_all(){
        $res = $this->findAll('color');
        return $res;
    }

    /**
     * 添加颜色，超过6个自动删除
     * @param $data
     * @return bool|mixed
     */
    public function set_one($data){
        $color = $data['color'];
        $count = $this->count("ke_color","1","color='$color'");
        if($count==0){
            $ret = $this->insert($data);
            $exist = $this->count('ke_color',1);
            if($exist>6){
                $this->delete("id<".($ret-5));
            }
        }else{
            $ret = false;
        }
        return $ret;
    }

}