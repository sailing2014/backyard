<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/7/16
 * Time: 13:37
 */
include_once MOB_PATH.'/MobiController.php';
class PurifierController extends MobiController{
    public function __construct()
    {
        parent::__construct();
    }

    public function IndexAction(){
        $this->view->display('mobi/purifier/index');
    }

}