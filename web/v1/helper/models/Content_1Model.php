<?php

class Content_1Model extends ContentModel {

    public function __construct()
    {
        parent::__construct();
    }

    public function get_primary_key() {
        return $this->primary_key = 'id';
    }

    public function get_fields() {
        return $this->get_table_fields();
    }
    
    public function get_by_id($id){
        
    }


}