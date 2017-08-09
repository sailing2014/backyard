<?php

class BabydataModel extends Model
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
}