<?php
namespace app\common\model;

/**
 * UserModel类
 * @author 贺强
 * @time   2019-01-08 15:24:22
 */
class UserModel extends CommonModel
{
    public function __construct()
    {
        $this->table = 'users';
    }
}