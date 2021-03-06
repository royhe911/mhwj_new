<?php
namespace app\api\controller;

use app\common\model\UserModel;

/**
 * 基本-控制器
 * @author 贺强
 * @time   2019-01-21 10:33:05
 */
class Api extends \think\Controller
{
    private $param = [];

    public function __construct()
    {
        $param = file_get_contents('php://input');
        $param = json_decode($param, true);
        if (empty($param['vericode'])) {
            echo json_encode(['status' => 300, 'info' => '非法参数', 'data' => null]);exit;
        }
        $vericode = $param['vericode'];
        unset($param['vericode']);
        $new_code = md5(config('MD5_PARAM'));
        if ($vericode !== $new_code) {
            echo json_encode(['status' => 100, 'info' => '非法参数', 'data' => null]);exit;
        }
        $this->param = $param;
    }

    /**
     * 用户登录
     * @author 贺强
     * @time   2019-01-21 10:36:02
     * @param  UserModel $u UserModel 实例
     * @return int          返回用户ID
     */
    public function user_login(UserModel $u)
    {
        $param = $this->param;
        if (empty($param['js_code'])) {
            $msg = ['status' => 1, 'info' => 'js_code 参数不能为空', 'data' => null];
            echo json_encode($msg);exit;
        }
        $js_code = $param['js_code'];
        $appid   = config('APPID');
        $secret  = config('APPSECRET');
        $url     = "https://api.weixin.qq.com/sns/jscode2session?appid={$appid}&secret={$secret}&js_code={$js_code}&grant_type=authorization_code";
        $data    = $this->curl($url);
        $data    = json_decode($data, true);
        if (empty($data['openid'])) {
            echo json_encode(['status' => 2, 'info' => 'code 过期', 'data' => null]);exit;
        }
        $user = $u->getModel(['openid' => $data['openid']]);
        if (!empty($user)) {
            $data['login_time'] = time();
            $data['count']      = $user['count'] + 1;
            // 修改数据
            $id  = $user['id'];
            $res = $u->modify($data, ['id' => $id]);
            if ($res) {
                $msg = ['status' => 0, 'info' => '登录成功', 'data' => ['id' => $user['id']]];
            } else {
                $msg = ['status' => 3, 'info' => '登录失败', 'data' => null];
            }
        } else {
            $data['addtime']    = time();
            $data['login_time'] = time();
            // 添加
            $id = $u->add($data);
            if ($id) {
                $msg = ['status' => 0, 'info' => '登录成功', 'data' => ['id' => $id]];
            } else {
                $msg = ['status' => 4, 'info' => '登录失败', 'data' => null];
            }
        }
        echo json_encode($msg);exit;
    }

    /**
     * 测试
     * @author 贺强
     * @time   2019-01-21 11:01:59
     */
    public function test()
    {
        echo json_encode(['status' => 0, 'info' => '测试']);exit;
    }
}
