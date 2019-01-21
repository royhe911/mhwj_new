<?php
namespace app\common\model;

/**
 * AdminModel类
 * @author 贺强
 * @time   2019-01-21 12:15:32
 */
class AdminModel extends CommonModel
{
    public function __construct()
    {
        $this->table = 't_admin';
    }
}