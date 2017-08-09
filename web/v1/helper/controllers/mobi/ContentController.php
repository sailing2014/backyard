<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 8/8 0008
 * Time: 15:48
 */

    include_once MOB_PATH.'/MobiController.php';
class ContentController extends MobiController {
    protected $contentModel;
    public function __construct()
    {
        parent::__construct();
        $this->contentModel = new Content_1Model();
    }

    public function ViewAction(){
        $id = $this->get('id');

    }
    
    public function detailAction(){
        $id = $this->get('id');       
    }

}