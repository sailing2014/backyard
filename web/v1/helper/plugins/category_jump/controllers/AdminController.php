<?php

class AdminController extends Plugin {

    private $tree;

    public function __construct() {
        parent::__construct();
        //Admin控制器进行登录验证
        $this->tree = $this->instance('tree');
        $this->tree->config(array('id' => 'catid', 'parent_id' => 'parentid', 'name' => 'catname'));
        if (!$this->session->is_set('user_id') || !$this->session->get('user_id'))
            $this->adminMsg('请登录以后再操作', url('admin/login'));
        $menu = array(
            array('index', '分类跳转列表'),
            array('add', '添加分类跳转'),
        );
        $this->assign('menu', $menu);
    }

    /*
     * 投票管理
     */

    public function indexAction() {
        if ($this->isPostForm()) {
            $ids = $this->post('ids');
            if ($ids) {
                foreach ($ids as $id) {
                    $this->category_jump->delete('id=' . $id);
                }
            }
        }
        $page = isset($_GET['page']) ? $this->get('page') : 1;
        $pagelist = $this->instance('pagelist');
        $pagelist->loadconfig();
        $pagesize = 8;
        $total = $this->category_jump->count('category_jump', 'id');
        $url = purl('admin/index', array('page' => '{page}'));
        $data = $this->category_jump->page_limit($page, $pagesize)->order('addtime DESC')->select();
        $pagelist = $pagelist->total($total)->url($url)->num($pagesize)->page($page)->output();
        $this->assign(array(
            'list' => $data,
            'pagelist' => $pagelist
        ));
        $this->display('list');
    }

    /*
     * 添加投票
     */

    public function addAction() {
        if ($this->isPostForm()) {
            $data = $this->post('data');
            if (empty($data['subject']))
                $this->adminMsg('请输入名称！');
            $data['addtime'] = time();
            $this->category_jump->insert($data);
            $this->adminMsg('添加成功！', purl('admin'), 3, 1, 1);
        }
        $this->assign(array(
            'category' => $this->tree->get_tree($this->cats, 0, 1)
        ));
        $this->display('add');
    }

    /*
     * 修改投票
     */

    public function editAction() {
        $id = $this->get('id');
        if (empty($id))
            $this->adminMsg('Id无效！');
        if ($this->isPostForm()) {
            $data = $this->post('data');
            if (empty($data['subject']))
                $this->adminMsg('请输入名称！');

            $this->category_jump->update($data, 'id=' . $id);
            $this->adminMsg('修改成功！', purl('admin'), 3, 1, 0);
        }
        $data = $this->category_jump->find($id);
        $this->assign(array(
            'data' => $data,
            'category' =>$this->tree->get_tree($this->cats, 0,  $data["cat_id"])
        ));
        if (empty($data))
            $this->adminMsg('名称不存在！');
        $this->display('add');
    }

}
