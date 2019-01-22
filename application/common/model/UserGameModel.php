<?php
namespace app\common\model;

/**
 * UserGameModel类
 * @author 贺强
 * @time   2019-01-22 12:12:21
 */
class UserGameModel extends CommonModel
{
    public function __construct()
    {
        $this->table = 't_user_game';
    }
}