<?php
namespace app\admin\controller;

use app\common\model\AdminModel;
use app\common\model\MenuModel;

/**
 * 后台管理
 */
class Index extends \think\Controller
{
    /**
     * 首页
     * @author 贺强
     * @time   2018-10-25 14:13:15
     */
    public function index()
    {
        $admin = session('admin');
        if ($admin) {
            $menu     = $this->get_menus();
            $m        = new MenuModel();
            $where    = [];
            $list     = $m->getList($where, 'identity,`name`');
            $list     = array_column($list, 'name', 'identity');
            $menu_str = json_encode($list);
            // echo $menu_str;exit;
            return $this->fetch('index', ['admin' => $admin, 'menu' => $menu, 'menu_str' => $menu_str]);
        }
        return $this->fetch('login');
    }

    /**
     * 根据用户角色 ID 获取该用户的权限
     * @author 贺强
     * @time   2018-10-25 14:13:07
     * @param  integer $role_id 用户角色 ID
     * @return json    返回该用户拥有权限菜单
     */
    private function get_menus($role_id = 0)
    {
        $where = ['is_hide' => 0];
        $m     = new MenuModel();
        $list  = $m->getList($where, true, null, 'pid,orders');
        $arr   = [];
        foreach ($list as $item) {
            if ($item['pid'] === 0) {
                $arr[] = $item;
            } else {
                foreach ($arr as &$item2) {
                    if ($item['pid'] == $item2['id']) {
                        $item2['children'][] = $item;
                        break;
                    }
                }
            }
        }
        return $arr;
    }

    /**
     * 后台欢迎页
     * @author 贺强
     * @time   2018-10-25 14:12:59
     */
    public function index_v1()
    {
        $admin = $this->is_login();
        return view('index_v1');
    }

    /**
     * 登录页
     * @author 贺强
     * @time   2018-10-25 14:11:50
     */
    public function login()
    {
        if ($this->request->get('action') === 'logout') {
            session('admin', null);
        }
        // return view('login', ['info'=>'']);
        return view('login');
    }

    /**
     * 登录操作
     * @author 贺强
     * @time   2018-10-25 14:11:40
     * @param  AdminModel $a AdminModel 实例
     * @return bool          返回登录是否成功
     */
    public function do_login(AdminModel $a)
    {
        $param = $this->request->post();
        if (empty($param['username']) || empty($param['pwd'])) {
            return ['status' => 3, 'info' => '用户名或密码不能为空'];
        }
        $admin = $a->getModel(['username|mobile' => $param['username']]);
        if (empty($admin)) {
            return ['status' => 2, 'info' => '用户名或密码错误'];
        }
        $pwd = get_password($param['pwd'], $admin['salt']);
        if ($pwd !== $admin['pwd']) {
            return ['status' => 1, 'info' => '用户名或密码错误'];
        }
        if ($admin['status'] !== 1) {
            return ['status' => 3, 'info' => '账号审核不通过或被禁用，请联系管理员'];
        }
        $a->modifyField('logintime', time(), ['id' => $admin['id']]);
        session('admin', $admin);
        return ['status' => 0, 'info' => '登录成功'];
    }

    /**
     * 添加用户账号
     * @author 贺强
     * @time   2018-10-25 14:11:14
     * @param  AdminModel $a AdminModel 实例
     */
    public function add(AdminModel $a)
    {
        $admin = $this->is_login();
        if ($this->request->isAjax()) {
            $param = $this->request->post();
            if (empty($param['username']) || empty($param['pwd'])) {
                return ['status' => 1, 'info' => '非法参数'];
            }
            $has = $a->getCount(['username' => $param['username']]);
            if (!empty($has)) {
                return ['status' => 2, 'info' => '该账号已存在'];
            }
            $salt             = get_random_str(); // 生成密码盐
            $param['salt']    = $salt;
            $param['pwd']     = get_password($param['pwd'], $salt);
            $param['addtime'] = time();
            // 添加
            $res = $a->add($param);
            if (!$res) {
                return ['status' => 4, 'info' => '添加失败'];
            }
            return ['status' => 0, 'info' => '添加成功'];
        } else {
            $time = time();
            return $this->fetch('add', ['time' => $time, 'token' => md5(config('UPLOAD_SALT') . $time)]);
        }
    }

    /**
     * 设置用户
     * @author 贺强
     * @time   2018-10-25 14:14:27
     * @param  AdminModel $a AdminModel 实例
     */
    public function operate(AdminModel $a)
    {
        $ids = $this->request->post('ids');
        if (empty($ids) || !is_array($ids)) {
            return ['status' => 3, 'info' => '非法参数'];
        }
        $type = $this->request->post('type');
        if (empty($type)) {
            return ['status' => 1, 'info' => '非法操作'];
        }
        if ($type === 'del' || $type === 'delAll') {
            $res = $a->delByWhere(['id' => $ids]);
            $msg = '删除';
        } else {
            if ($type === 'disable' || $type === 'disableAll') {
                $field = 'status';
                $value = 44;
                $msg   = '禁用';
            } elseif ($type == 'enable' || $type == 'enableAll') {
                $field = 'status';
                $value = 1;
                $msg   = '启用';
            } else {
                return ['status' => 2, 'info' => '非法操作'];
            }
            $res = $a->modifyField($field, $value, ['id' => $ids]);
        }
        if ($res !== false) {
            return ['status' => 0, 'info' => $msg . '成功'];
        } else {
            return ['status' => 4, 'info' => $msg . '失败'];
        }
    }

    /**
     * 修改用户
     * @author 贺强
     * @time   2018-10-25 14:13:59
     * @param  AdminModel $a AdminModel 实例
     */
    public function edit(AdminModel $a)
    {
        $admin = $this->is_login();
        if ($this->request->isAjax()) {
            $param    = $this->request->post();
            $id       = $admin['id'];
            $username = $admin['username'];
            if ($username === 'admin') {
                $id = $param['id'];
            } elseif ($admin['username'] !== $param['username']) {
                return ['status' => 1, 'info' => '非法操作，只能修改自己的账号'];
            }
            $is_pwd = false;
            if (!empty($param['pwd'])) {
                $salt          = get_random_str(15); // 生成密码盐
                $param['salt'] = $salt;
                $param['pwd']  = get_password($param['pwd'], $salt);
                if ($admin['username'] === $param['username']) {
                    $is_pwd = true;
                }
            } else {
                unset($param['pwd']);
            }
            if (empty($param['avatar'])) {
                unset($param['avatar']);
            }
            unset($param['username']);
            // 执行修改
            $res = $a->modify($param, ['id' => $id]);
            if (!$res) {
                return ['status' => 4, 'info' => '修改失败'];
            }
            $msg    = '修改成功';
            $status = 0;
            if ($is_pwd) {
                $status = 88;
                $msg    = '密码重修成功，请重新登录';
            }
            return ['status' => $status, 'info' => $msg];
        } else {
            $id = $this->request->get('id');
            if (empty($id) || !is_numeric($id)) {
                $id = $admin['id'];
            }
            $user = $a->getModel(['id' => $id]);
            if (empty($user)) {
                $this->error('用户不存在');
            }
            $time = time();
            return $this->fetch('edit', ['admin' => $user, 'time' => $time, 'token' => md5(config('UPLOAD_SALT') . $time)]);
        }
    }

    /**
     * 用户列表
     * @author 贺强
     * @time   2018-10-25 14:14:10
     * @param  AdminModel $a AdminModel 实例
     */
    public function lists(AdminModel $a)
    {
        $where   = [];
        $keyword = '';
        $param   = $this->request->get();
        // print_r($param);exit;
        if (!empty($param['keyword'])) {
            $where[] = ['mobile', 'like', "%{$param['keyword']}%"];
            $keyword = $param['keyword'];
        }
        // 分页参数
        $page = 1;
        if (!empty($param['page'])) {
            $page = $param['page'];
        }
        $pagesize = 10;
        if (!empty($param['pagesize'])) {
            $pagesize = $param['pagesize'];
        }
        $list = $a->getList($where, true, "$page,$pagesize", 'logintime desc');
        // print_r($list);exit;
        foreach ($list as &$item) {
            $item['status_txt'] = get_user_status($item['status']);
            if (!empty($item['logintime'])) {
                $item['logintime'] = date('Y-m-d H:i:s', $item['logintime']);
            }
            if (!empty($item['addtime'])) {
                $item['addtime'] = date('Y-m-d H:i:s', $item['addtime']);
            }
        }
        $count = $a->getCount($where);
        $pages = ceil($count / $pagesize);
        return $this->fetch('list', ['list' => $list, 'keyword' => $keyword, 'pages' => $pages]);
    }

}
