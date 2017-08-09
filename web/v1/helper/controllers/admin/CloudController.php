<?php
include_once CONTROLLER_DIR.'admin/Common.php';
class CloudController extends Admin {
	protected $httpclient;
    public function __construct() {
		parent::__construct();
		$this->httpclient	= $this->instance('http_client');
	}

	public function indexAction(){
		$this->view->display('admin/cloud/index');
	}

}