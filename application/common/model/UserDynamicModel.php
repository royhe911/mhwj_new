<?php
namespace app\common\model;

/**
 * UserDynamicModel类
 * @author 贺强
 * @time   2019-01-22 12:10:33
 */
class UserDynamicModel extends CommonModel
{
    public function __construct()
    {
        $this->table = 't_user_dynamic';
    }
}