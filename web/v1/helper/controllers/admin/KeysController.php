<?php

class KeysController extends Admin {
    
    protected $keys;
    
    public function __construct() {
		parent::__construct();
		$this->keys = $this->model('keys');
	}
	
	public function indexAction() {
	    if ($this->post('submit_del') && $this->post('form') == 'del') {
	        foreach ($_POST as $var=>$value) {
	            if (strpos($var, 'del_') !== false) {
	                $id = (int)str_replace('del_', '', $var);
	                $this->delAction($id, 1);
	            }
	        }
			$this->adminMsg($this->getCacheCode('key') . lang('success'), url('admin/keys/'), 3, 1, 1);
	    } elseif ($this->post('submit_update') && $this->post('form') == 'update') {
	        $data = $this->post('data');
			if (empty($data)) $this->adminMsg(lang('a-key-0'));
			foreach ($data as $id=>$t) {
			    $this->keys->update($t, 'id=' . $id);
			}
			$this->adminMsg($this->getCacheCode('keys') . lang('success'), url('admin/keys/'), 3, 1, 1);
	    }
	    $page     = (int)$this->get('page');
		$page     = (!$page) ? 1 : $page;
	    $pagelist = $this->instance('pagelist');
		$pagelist->loadconfig();
	    if ($this->post('submit')) $kw = $this->post('kw');
	    $where    = null;
	    $kw       = $kw ? $kw : $this->get('kw');
	    if ($kw) $where = "name like '%" . $kw . "%'";
	    $total    = $this->keys->count('keys', null, $where);
	    $pagesize = isset($this->site['SITE_ADMIN_PAGESIZE']) && $this->site['SITE_ADMIN_PAGESIZE'] ? $this->site['SITE_ADMIN_PAGESIZE'] : 8;
	    $url      = url('admin/keys/index', array('page'=>'{page}', 'kw'=>$kw));
	    $select   = $this->keys->page_limit($page, $pagesize)->order(array('listorder DESC','id DESC'));
	    if ($where) $select->where($where);
	    $data     = $select->select();
	    $pagelist = $pagelist->total($total)->url($url)->num($pagesize)->page($page)->output();
	    $this->view->assign(array(
	        'list'     => $data,
	        'pagelist' => $pagelist,
	    ));
	    $this->view->display('admin/keys/list');
	}
	
	public function addAction() {
	    if ($this->is_post()) {
	        $data = $this->post('data');
	        if (empty($data['name']))   $this->adminMsg(lang('a-keys-1'));
			if (empty($data['letter'])) $data['letter'] = word2pinyin($data['letter']);
            $count = $this->keys->count("keys","*","name='".$data['name']."'");
            if($count){
                $this->adminMsg("数据重复", url('admin/keys'));
            }else{
                $this->keys->insert($data);
                $this->adminMsg($this->getCacheCode('keys') . lang('success'), url('admin/keys'), 3, 1, 1);
            }
	    }
	    $this->view->display('admin/keys/add');
	}
	
    public function editAction() {
        $id   = (int)$this->get('id');
        if($this->is_post()){
            $data = $this->post('data');
            if (empty($data['name'])) $this->adminMsg(lang('a-key-1'));
            if (empty($data['letter'])) $data['letter'] = word2pinyin($data['letter']);
            $count = $this->keys->count("keys","*","id!=$id and name='".$data['name']."'");
            if($count){
                $this->adminMsg("数据重复", url('admin/keys'));
            }else{
                $this->keys->update($data, 'id=' . $id);
                $this->adminMsg($this->getCacheCode('keys') . lang('success'), url('admin/keys'), 3, 1, 1);
            }
        }else{
            $data = $this->keys->find($id);
            if (empty($data)) $this->adminMsg(lang('a-key-2'));
            $this->view->assign('data', $data);
        }
	    $this->view->display('admin/keys/add');
	}
	
    public function delAction($id=0, $all=0) {
        if (!auth::check($this->roleid, 'keys-del', 'admin')) $this->adminMsg(lang('a-com-0', array('1'=>'keys', '2'=>'del')));
	    $all = $all ? $all : $this->get('all');
		$id  = $id  ? $id  : (int)$this->get('id');
	    $this->keys->delete('id=' . $id);
	    $all or $this->adminMsg($this->getCacheCode('keys') . lang('success'), url('admin/keys/index'), 3, 1, 1);
	}
	
	public function importAction() {
	    if ($this->post('submit')) {
	        $i    = $j = $k = 0;
	        $file = $_FILES['txt'];
	        if ($file['type'] != 'text/plain') $this->adminMsg(lang('a-key-3', array('1'=>$file['type'])));
	        if ($file['error']) $this->adminMsg(lang('a-key-4'));
	        if (!file_exists($file['tmp_name'])) $this->adminMsg(lang('a-key-5'));
	        $data = file_get_contents($file['tmp_name']);
	        $data = explode(PHP_EOL, $data);
	        foreach ($data as $t) {
	            $name = trim($t);
	            if ($name) {
	                $row = $this->keys->getOne('name=?', $name);
	                if (empty($row)) {
	                    $id = $this->keys->insert(array('name'=>$name, 'letter'=>word2pinyin($name)));
	                    if ($id) $i++;
	                } else {
	                    $j ++;
	                }
	            } else {
	                $k ++;
	            }
	        }
	        $this->adminMsg($this->getCacheCode('keys') . lang('a-key-6', array('1'=>$i, '2'=>$k, '3'=>$j)), url('admin/keys/index'), 3, 1, 1);
	    }
	    $this->view->display('admin/keys/import');
	}
	
	public function cacheAction($show=0) {
	    $qok  = $this->get('qok');
		if ($show == 0 && !$qok) $this->adminMsg(lang('a-key-20'), url('admin/keys/cache', array('qok'=>1)), 0, 1, 2);
	    $data = $this->keys->from(null, 'name,letter')->order('listorder DESC, id DESC')->select();
		$list = array();
		if ($data) {
		    $cfg = Controller::load_config('config');
		    foreach ($data as $t) {
			    $list[$t['name']] = array(
				    'name' => $t['name'],
					'url'  => $cfg['SITE_KEY_URL'] ? str_replace('{key}', $t['letter'], SITE_PATH . $cfg['SITE_KEY_URL']) : url('keys/list', array('kw'=>$t['letter']))
				);
			}
		}
	    $this->cache->set('keys', $list);
	    $show or $this->adminMsg(lang('a-update'), url('admin/keys'), 3, 1, 1);
	}
}