<?php

class TryoutModel extends Model {

    /**
     *
     */
    public function get_all($page,$size){
        $count = $this->count('tryout');
        $result['count'] = $count;
        $result['data'] = $this->page_limit($page,$size)->select();
        return $result;
    }

    /**
     * @param string $phone
     */
    public function add($phone){
        if(empty($phone)){
            return false;
        }
        $result = $this->from('','count(*) as count')->where("phone='$phone'")->select(false);
        if($result['count']>0){
            return 2;
        }
        $data = array('phone'=>$phone,'addtime'=>time());
        $result = $this->insert($data);
        if($result){
            return 1;
        }else{
            return 0;

        }
    }

    /**
     *
     */
    public function del($ids){
        if(empty($ids)){
            return false;
        }
        $result = $this->delete("id in($ids)");
        if($result){
            return true;
        }else{
            return false;
        }
    }

}