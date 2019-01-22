<?php
namespace app\admin\controller;

use app\common\model\CircleModel;

/**
 * 圈子-控制器
 * @author 贺强
 * @time   2019-01-22 09:49:15
 */
class Circle extends \think\Controller
{
    /**
     * 添加圈子
     * @author 贺强
     * @time   2019-01-22 09:58:49
     * @param  CircleModel $c CircleModel 实例
     */
    public function add(CircleModel $c)
    {
        $admin = $this->is_login();
        if ($this->request->isAjax()) {
            $param = $this->request->post();
            // 添加时间
            $param['addtime'] = time();
            // 添加
            $res = $c->add($param);
            if (!$res) {
                return ['status' => 4, 'info' => '添加失败'];
            } else {
                return ['status' => 0, 'info' => '添加成功'];
            }
        } else {
            return $this->fetch('add');
        }
    }

    /**
     * 删除圈子
     * @author 贺强
     * @time   2019-01-22 10:30:31
     * @param  CircleModel $c CircleModel 实例
     */
    public function del(CircleModel $c)
    {
        $this->is_login();
        if ($this->request->isAjax()) {
            $id  = $this->request->post('id');
            $res = $c->delById($id);
            if (!$res) {
                return ['status' => 4, 'info' => '删除失败'];
            }
            return ['status' => 0, 'info' => '删除成功'];
        }
    }

    /**
     * 修改圈子排序
     * @author 贺强
     * @time   2019-01-22 10:35:40
     * @param  CircleModel $c CircleModel 实例
     */
    public function esort(CircleModel $c)
    {
        if ($this->request->isAjax()) {
            $param = $this->request->post();
            if (empty($param['id'])) {
                return ['status' => 1, 'info' => '非法参数'];
            }
            $res = $c->modifyField('sort', $param['sort'], ['id' => $param['id']]);
            if ($res === false) {
                return ['status' => 2, 'info' => '修改失败'];
            }
            return ['status' => 0, 'info' => '修改成功'];
        }
    }

    /**
     * 修改圈子
     * @author 贺强
     * @time   2019-01-22 10:24:20
     * @param  CircleModel $c CircleModel 实例
     */
    public function edit(CircleModel $c)
    {
        $admin = $this->is_login();
        if ($this->request->isAjax()) {
            $param = $this->request->post();
            if (empty($param['id']) || empty($param['name'])) {
                return ['status' => 3, 'info' => '非法操作'];
            }
            $res = $c->modify($param, ['id' => $param['id']]);
            if (!$res) {
                return ['status' => 4, 'info' => '修改失败'];
            } else {
                return ['status' => 0, 'info' => '修改成功'];
            }
        } else {
            $circle = null;
            $id     = $this->request->get('id');
            if (!empty($id) && is_numeric($id)) {
                $circle = $c->getModel(['id' => $id]);
            }
            return $this->fetch('edit', ['circle' => $circle]);
        }
    }

    /**
     * 圈子列表
     * @author 贺强
     * @time   2019-01-22 10:34:16
     * @param  CircleModel $c CircleModel 实例
     */
    public function lists(CircleModel $c)
    {
        $admin = $this->is_login();
        $where = [];
        // 分页参数
        $page     = $this->request->get('page', 1);
        $pagesize = $this->request->get('pagesize', config('PAGESIZE'));
        $list     = $c->getList($where, true, "$page,$pagesize", 'sort');
        // var_dump($list);exit;
        $count = $c->getCount($where);
        $pages = ceil($count / $pagesize);
        return $this->fetch('list', ['list' => $list, 'pages' => $pages]);
    }
}
