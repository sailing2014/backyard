<?php

class Content_1_dailyModel extends Model {

    public function get_primary_key() {
        return $this->primary_key = 'id';
    }

    public function get_fields() {
        return $this->get_table_fields();
    }

    public function get_daily($day,$field="title,thumb,description"){
        $day = $day ? $day : 0;
        $sql = " SELECT d.id,d.catid,$field FROM fn_content_1_daily d LEFT  JOIN fn_content_1 c ON d.id=c.id WHERE d.day=$day LIMIT 1";
//        var_dump($sql);
        $res = $this->execute($sql);
        return $res[0];
    }

}