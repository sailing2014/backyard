<?php
include_once CONTROLLER_DIR.'Cloud.php';
class VideoController extends Cloud{
    public function __construct() {
		parent::__construct();
	}

	public function indexAction(){
        $this->view->display('admin/video/index');
	}

}