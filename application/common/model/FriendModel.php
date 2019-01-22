<?php
namespace app\common\model;

/**
 * FriendModel类
 * @author 贺强
 * @time   2019-01-22 12:13:13
 */
class FriendModel extends CommonModel
{
    public function __construct()
    {
        $this->table = 't_friend';
    }
}