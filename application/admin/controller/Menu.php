<?php
namespace app\admin\controller;

use app\common\model\MenuModel;

class Menu extends \think\Controller
{
    /**
     * 添加菜单
     * @author 贺强
     * @time   2019-01-21 17:15:41
     * @param  MenuModel $m MenuModel 实例
     */
    public function add(MenuModel $m)
    {
        $admin = $this->is_login();
        if ($this->request->isAjax()) {
            $param = $this->request->post();
            if (empty($param['identity']) || empty($param['name'])) {
                return ['status' => 3, 'info' => '非法操作'];
            }
            $menu = $m->getModel(['identity' => $param['identity']]);
            if (!empty($menu)) {
                return ['status' => 1, 'info' => '菜单标识重复，请重新填写'];
            }
            if (empty($param['orders'])) {
                $param['orders'] = 99;
            }
            $res = $m->add($param);
            if ($res) {
                return ['status' => 0, 'info' => '添加成功'];
            }
            return ['status' => 2, 'info' => '添加失败'];
        } else {
            $menu = $m->getList(['pid' => 0]);
            return $this->fetch('add', ['menu' => $menu]);
        }
    }

    /**
     * 删除菜单
     * @author 贺强
     * @time   2019-01-21 17:16:13
     * @param  MenuModel $m MenuModel 实例
     */
    public function del(MenuModel $m)
    {
        $admin = $this->is_login();
        $id    = $this->request->post('id');
        $res   = $m->delById($id);
        if ($res) {
            return ['status' => 0, 'info' => '删除成功'];
        }
        return ['status' => 4, 'info' => '删除失败'];
    }

    /**
     * 编辑菜单
     * @author 贺强
     * @time   2019-01-21 17:18:37
     * @param  MenuModel $m MenuModel 实例
     */
    public function edit(MenuModel $m)
    {
        $admin = $this->is_login();
        if ($this->request->isAjax()) {
            $param = $this->request->post();
            if (empty($param['id']) || empty($param['identity']) || empty($param['name'])) {
                return ['status' => 3, 'info' => '非法操作'];
            }
            $res = $m->modify($param, ['id' => $param['id']]);
            if ($res !== false) {
                $m->modifyField('orders', $param['orders'], ['pid' => $param['id']]);
                return ['status' => 0, 'info' => '修改成功'];
            } else {
                return ['status' => 4, 'info' => '修改失败'];
            }
        } else {
            $menu = null;
            $id   = $this->request->get('id');
            if (!empty($id) && is_numeric($id)) {
                $menu = $m->getModel(['id' => $id]);
            }
            return $this->fetch('edit', ['menu' => $menu]);
        }
    }

    /**
     * 菜单列表
     * @author 贺强
     * @time   2019-01-21 17:19:17]
     */
    public function lists()
    {
        $admin = $this->is_login();
        $m     = new MenuModel();
        $where = [];
        // 分页参数
        $page     = $this->request->get('page', 1);
        $pagesize = $this->request->get('pagesize', config('PAGESIZE'));
        $list     = $m->getList($where, true, "$page,$pagesize", 'orders');
        // var_dump($list);exit;
        $count = $m->getCount($where);
        $pages = ceil($count / $pagesize);
        return $this->fetch('list', ['list' => $list, 'pages' => $pages]);
    }

}
