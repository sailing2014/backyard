<?php
include_once CONTROLLER_DIR.'Cloud.php';
class FamilyController extends Cloud{
    public function __construct() {
		parent::__construct();
	}

	public function indexAction(){
        $this->view->display('admin/family/index');
	}

}