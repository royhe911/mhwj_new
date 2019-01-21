<?php
namespace app\admin\controller;

use app\common\model\ChatUserModel;
use app\common\model\GameModel;
use app\common\model\MessageModel;
use app\common\model\RoomMasterModel;
use app\common\model\RoomModel;
use app\common\model\UserAttrModel;
use app\common\model\UserModel;

/**
 * User-控制器
 * @author 贺强
 * @time   2018-10-29 12:19:03
 */
class User extends \think\Controller
{
    /**
     * 用户列表
     * @author 贺强
     * @time   2018-10-29 12:21:17
     * @param  UserModel $u UserModel 实例
     * @return array            返回用户列表数据集
     */
    public function lists(UserModel $u)
    {
        $where = 'is_delete=0';
        $param = $this->request->get();
        if (!empty($param['type'])) {
            $where .= " and `type`={$param['type']}";
        } else {
            $param['type'] = 0;
        }
        if (!empty($param['is_recommend']) && intval($param['is_recommend']) === 1) {
            $where .= " and is_recommend=1";
        } else {
            $param['is_recommend'] = '';
        }
        if (!empty($param['status'])) {
            $where .= " and type=2 and (`status`={$param['status']}";
            if (intval($param['status']) === 1) {
                $where .= " or `status`=0";
            }
            $ua  = new UserAttrModel();
            $uas = $ua->getList(['status' => $param['status']], ['uid']);
            $ids = "0";
            if (!empty($uas)) {
                foreach ($uas as $us) {
                    $ids .= ",{$us['uid']}";
                }
            }
            if ($ids !== "0") {
                $where .= " or id in ($ids)";
            }
            $where .= ")";
        } else {
            $param['status'] = 0;
        }
        if (!empty($param['nickname'])) {
            $where .= " and nickname like '%{$param['nickname']}%'";
        } else {
            $param['nickname'] = '';
        }
        // 分页参数
        $page     = intval($this->request->get('page', 1));
        $pagesize = intval($this->request->get('pagesize', config('PAGESIZE')));
        $list     = $u->getList($where, true, "$page,$pagesize",'addtime desc,sort desc');
        foreach ($list as &$item) {
            if ($item['type'] === 1) {
                $item['type_txt'] = '玩家';
            } elseif ($item['type'] === 2) {
                $item['type_txt'] = '陪玩师';
            } else {
                $item['type_txt'] = '';
            }
            if ($item['status'] === 8) {
                $item['status_txt'] = '已审核';
            } elseif ($item['status'] === 1) {
                $item['status_txt'] = '待审核';
            } elseif ($item['status'] === 4) {
                $item['status_txt'] = '审核不通过';
            } else {
                if ($item['type'] === 2) {
                    $item['status_txt'] = '待审核';
                } else {
                    $item['status_txt'] = '普通玩家';
                }
            }
            if (!empty($item['addtime'])) {
                $item['addtime'] = date('Y-m-d H:i:s', $item['addtime']);
            }
            if ($item['sex'] === 1) {
                $item['sex'] = '男';
            } elseif ($item['sex'] === 2) {
                $item['sex'] = '女';
            } else {
                $item['sex'] = '保密';
            }
        }
        $count = $u->getCount($where);
        $pages = ceil($count / $pagesize);
        return $this->fetch('list', ['list' => $list, 'pages' => $pages, 'param' => $param]);
    }

    /**
     * 用户详情
     * @author 贺强
     * @time   2018-10-29 15:46:55
     * @param  UserModel     $u  UserModel 实例
     */
    public function detail(UserModel $u)
    {
        $id = $this->request->get('id');
        if (!preg_match('/^\d+$/', $id)) {
            echo "非法参数";exit;
        }
        $user = $u->getModel(['id' => $id]);
        if (empty($user)) {
            echo "用户不存在";exit;
        }
        $user['type_txt'] = '玩家';
        if ($user['type'] === 2 || $user['status'] === 1 || $user['status'] === 8) {
            $user['type_txt'] = '陪玩师';
            $ua               = new UserAttrModel();
            $attrs            = $ua->getList(['uid' => ['in', $id]]);
            $g                = new GameModel();
            $games            = $g->getList(['is_delete' => 0], 'id,`name`,`url`');
            $games            = array_column($games, null, 'id');
            foreach ($attrs as &$attr) {
                if (!empty($games[$attr['game_id']])) {
                    $attr['game_name'] = $games[$attr['game_id']]['name'];
                } else {
                    $attr['game_name'] = '';
                }
                if ($attr['play_type'] === 1) {
                    $attr['play_type'] = '实力上分';
                } elseif ($attr['play_type'] === 2) {
                    $attr['play_type'] = '娱乐陪玩';
                }
                if ($attr['status'] === 8) {
                    $attr['status_txt'] = '已审核';
                } elseif ($attr['status'] === 4) {
                    $attr['status_txt'] = '审核未通过';
                } else {
                    $attr['status_txt'] = '未审核';
                }
                $urls   = explode(',', $attr['level_url']);
                $urls[] = $attr['logo'];
                // 水平截图重新赋值
                $attr['level_url'] = $urls;
            }
            // print_r($attrs);exit;
            $user['attrs'] = $attrs;
        }
        if ($user['sex'] === 1) {
            $user['sex'] = '男';
        } elseif ($user['sex'] === 3) {
            $user['sex'] = '女';
        } else {
            $user['sex'] = '保密';
        }
        if ($user['status'] === 0) {
            $user['status_txt'] = '正常';
        } elseif ($user['status'] === 1) {
            $user['status_txt'] = '待审核';
        } elseif ($user['status'] === 4) {
            $user['status_txt'] = '审核不通过';
        } else {
            $user['status_txt'] = '';
        }
        if (!empty($user['addtime'])) {
            $user['addtime'] = date('Y-m-d H:i:s', $user['addtime']);
        }
        if (!empty($user['login_time'])) {
            $user['login_time'] = date('Y-m-d H:i:s', $user['login_time']);
        }

        return $this->fetch('detail', ['user' => $user]);
    }

    /**
     * 用户审核
     * @author 贺强
     * @time   2018-11-01 17:27:55
     * @param  UserModel     $u  UserModel 实例
     * @param  UserAttrModel $ua UserAttrModel 实例
     */
    public function auditor(UserModel $u, UserAttrModel $ua)
    {
        if ($this->request->isAjax()) {
            $param = $this->request->post();
            if (empty($param['id']) || empty($param['status'])) {
                return ['status' => 1, 'info' => '非法参数'];
            }
            $is_skill = 0;
            if (!empty($param['is_skill'])) {
                $is_skill = $param['is_skill'];
            }
            unset($param['is_skill']);
            if (intval($param['status']) === 8) {
                if ($is_skill) {
                    $content = '恭喜你，游戏技能审核通过';
                } else {
                    $param['type'] = 2;
                    $content       = "恭喜你，陪玩师认证审核已通过";
                }
            } else {
                if ($is_skill) {
                    $content = "技能审核未通过，原因：" . $param['reason'];
                } else {
                    $param['type'] = 1;
                    $content       = "陪玩师认证审核未通过，原因：" . $param['reason'];
                }
            }
            $m = new MessageModel();
            if ($is_skill) {
                $res = $ua->modify($param, ['id' => $param['id']]);
                if ($res) {
                    $data = ['type' => 1, 'uid' => $param['id'], 'title' => '系统消息', 'content' => $content, 'addtime' => time()];
                    $m->add($data);
                    return ['status' => 0, 'info' => '审核成功'];
                }
            } else {
                $res = $u->modify($param, ['id' => $param['id']]);
                if ($res) {
                    $user    = $u->getModel(['id' => $param['id']]);
                    $addtime = date('Y年m月d日 H:i:s', $user['addtime']);
                    $status  = '审核通过';
                    if (intval($param['status']) !== 8) {
                        $status = '审核未通过';
                    }
                    $this->shenhe_notice($user['openid'], $user['form_id'], $addtime, $user['nickname'], $status, $content);
                    $data = ['type' => 1, 'uid' => $param['id'], 'title' => '系统消息', 'content' => $content, 'addtime' => time()];
                    $m->add($data);
                    return ['status' => 0, 'info' => '审核成功'];
                }
            }
            return ['status' => 4, 'info' => '审核失败'];
        }
    }

    /**
     * 修改排序
     * @author 贺强
     * @time   2019-01-04 16:45:55
     * @param  UserModel $u UserModel 实例
     */
    public function editsort(UserModel $u)
    {
        if ($this->request->isAjax()) {
            $param = $this->request->post();
            if (empty($param['id']) || empty($param['sort'])) {
                return ['status' => 1, 'info' => '非法参数'];
            }
            $sort = $param['sort'];
            $res  = $u->modifyField('sort', $sort, ['id' => $param['id']]);
            if ($res === false) {
                return ['status' => 2, 'info' => '修改失败'];
            }
            return ['status' => 0, 'info' => '修改成功'];
        }
    }

    /**
     * 审核通知
     * @author 贺强
     * @time   2018-12-18 20:22:38
     * @param  string $openid    陪玩师OPENID
     * @param  string $form_id   FORMID
     * @param  string $addtime   申请时间
     * @param  string $nickanme  陪玩师昵称
     * @param  string $status    审核状态
     * @param  string $remark    备注
     */
    public function shenhe_notice($openid, $form_id, $addtime, $nickname, $status, $remark)
    {
        $access_token = $this->get_access_token(true);
        if ($access_token === false) {
            // 记录日志
        }
        // API 地址
        $url = 'https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send';
        $url .= "?access_token=$access_token";
        $data['touser'] = $openid;
        // 下单成功模板ID
        $data['template_id'] = 'KVSRU0TCSnf9yiJhrVnT4dJW5VvLxNXS4LqbmkPFNRM';
        $data['form_id']     = $form_id;
        $data['page']        = '/pages/fast/fast';
        $data['data']        = ['keyword1' => ['value' => date('Y年m月d日 H:i:s')], 'keyword2' => ['value' => $nickname], 'keyword3' => ['value' => $status], 'keyword4' => ['value' => $addtime], 'keyword5' => ['value' => $remark]];
        // 处理逻辑
        $data = json_encode($data);
        $res  = $this->curl($url, $data);
        $res  = json_decode($res, true);
        if (!empty($res['errcode'])) {
            // 记录日志
        }
    }

    /**
     * 设置用户
     * @author 贺强
     * @time   2018-11-05 14:40:24
     * @param  UserModel $u UserModel 实例
     */
    public function operate(UserModel $u)
    {
        $param = $this->request->post();
        if (empty($param['type'])) {
            return ['status' => 1, 'info' => '非法操作'];
        }
        if (empty($param['ids']) || !preg_match('/^0[\,\d+]+$/', $param['ids'])) {
            return ['status' => 2, 'info' => '非法参数'];
        }
        $ids = $param['ids'];
        if ($param['type'] === 'del') {
            $r = new RoomModel();
            $r->delByWhere(['uid' => ['in', $ids]]);
            $rm = new RoomMasterModel();
            $rm->delByWhere(['uid' => ['in', $ids]]);
            $cu = new ChatUserModel();
            $cu->delByWhere(['uid' => ['in', $ids]]);
            $res = $u->delByWhere(['id' => ['in', $ids]]);
            if ($res) {
                return ['status' => 0, 'info' => '删除成功'];
            }
        }
        switch ($param['type']) {
            case 'del':
                $field = 'is_delete';
                $value = 1;
                $msg   = '删除';
                break;
            case 'recommend':
                $field = 'is_recommend';
                $value = 1;
                $msg   = '推荐';
                break;
            case 'unrecommend':
                $field = 'is_recommend';
                $value = 0;
                $msg   = '取消推荐';
                break;
            default:
                $field = '';
                $value = '';
                $msg   = '';
                break;
        }
        if (empty($field) || $value === '') {
            return ['status' => 3, 'info' => '非法操作'];
        }
        $res = $u->modifyField($field, $value, ['id' => ['in', $param['ids']]]);
        if (!$res) {
            return ['status' => 4, 'info' => $msg . '失败'];
        }
        return ['status' => 0, 'info' => $msg . '成功'];
    }
}
