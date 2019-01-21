<?php
namespace app\common\model;

/**
 * MenuModel类
 * @author 贺强
 * @time   2019-01-21 12:16:22
 */
class MenuModel extends CommonModel
{
    public function __construct()
    {
        $this->table = 't_menu';
    }
}