<?php
namespace app\common\model;

/**
 * UserDynamicCommentModel类
 * @author 贺强
 * @time   2019-01-22 12:11:14
 */
class UserDynamicCommentModel extends CommonModel
{
    public function __construct()
    {
        $this->table = 't_user_dynamic_comment';
    }
}