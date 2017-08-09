<?php

class IndexController extends Plugin {

    public function __construct() {
        parent::__construct();
    }

    public function showAction() {
        $month = $this->get('month');

        if (empty($month))
            $month = "0";
        $data = $this->category_jump->getOne("(start<=? AND ?<end) OR start=0 ORDER BY start DESC LIMIT 0,1", $month);
        $this->redirect(url('content/list', array('catid' => $data["cat_id"])));
    }

}
