<?php
namespace app\api\controller;

use app\common\model\UserDynamicModel;

/**
 * 消息-控制器
 * @author 贺强
 * @time   2019-01-22 09:43:24
 */
class Message extends \think\Controller
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
     * 发布动态
     * @author 贺强
     * @time   2019-01-22 12:28:56
     * @param  UserDynamicModel $ud UserDynamicModel 实例
     */
    public function release(UserDynamicModel $ud)
    {
        $param = $this->param;
        if (empty($param['uid'])) {
            $msg = ['status' => 1, 'info' => '用户ID不能为空'];
        } elseif (empty($param['content'])) {
            $msg = ['status' => 3, 'info' => '动态内容不能为空'];
        }
        if (!empty($msg)) {
            echo json_encode($msg);exit;
        }
        $param['addtime'] = time();
        // 添加
        $res = $ud->add($param);
        if (!$res) {
            echo json_encode(['status' => 44, 'info' => '发布失败']);exit;
        }
        echo json_encode(['status' => 0, 'info' => '发布成功']);exit;
    }
}
