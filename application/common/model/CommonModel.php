<?php
namespace app\common\model;

use think\Db;
use think\Model;

class CommonModel extends Model
{
    public $table = '';

    /**
     * 添加一条数据
     * @author 贺强
     * @time   2016-11-16 15:57:17
     * @param array $data 要添加的数据 形如：['name'=>'think', 'sex'=>'男', ……]
     * @param  boolean $sql   是否打印 SQL
     * @return int 返回添加成功后的 ID，若没有自增ID则返回 true
     */
    public function add($data, $sql = false)
    {
        $id = Db::table($this->table)
            ->fetchSql($sql)
            ->insertGetId($data);
        if ($id === 0) {
            $id = true;
        }
        return $id;
    }

    /**
     * 添加多条数据
     * @author 贺强
     * @time   2016-11-16 15:57:17
     * @param array $data 要添加的数据 形如：[['name'=>'think', 'sex'=>'男', ……], ['name'=>'think', 'sex'=>'男', ……], ……]
     * @param  boolean $sql   是否打印 SQL
     * @return int 返回添加成功的条数
     */
    public function addArr($data, $sql = false)
    {
        $num = Db::table($this->table)
            ->fetchSql($sql)
            ->insertAll($data);
        return $num;
    }

    /**
     * 根据主键 ID
     * @author 贺强
     * @time   2016-11-16 15:57:17
     * @param int|array $id 要删除的数据的 ID 形如：1或者[1, 2, 3, ……]
     * @param  boolean $sql   是否打印 SQL
     * @return int 返回影响数据的条数，没有删除返回 0
     */
    public function delById($id, $sql = false)
    {
        $num = Db::table($this->table)
            ->fetchSql($sql)
            ->delete($id);
        return $num;
    }

    /**
     * 根据条件删除数据
     * @author 贺强
     * @time   2016-11-16 15:57:17
     * @param array|string $where 要删除数据的条件 形如：[['name','=','think'],['id','>',3], ……]或者 SQL 原生字符串，默认为空
     * @param  boolean $sql   是否打印 SQL
     * @return int 返回影响数据的条数，没有删除返回 0
     */
    public function delByWhere($where = null, $sql = false)
    {
        $num = Db::table($this->table)
            ->where($where)
            ->fetchSql($sql)
            ->delete();
        return $num;
    }

    /**
     * 根据条件修改数据
     * @author 贺强
     * @time   2016-11-16 15:57:17
     * @param array $data 要修改的数据 形如：['login_time'  => ['exp','now()'], 'name' => 'thinkphp', ……]
     * @param array|string $where 修改条件 形如：[['name','=','think'],['id','>',3], ……]或者 SQL 原生字符串，默认更新所有
     * @param  boolean $sql   是否打印 SQL
     * @return int 返回影响数据的条数，没修改任何数据返回 0
     */
    public function modify($data, $where = '1=1', $sql = false)
    {
        $num = Db::table($this->table)
            ->where($where)
            ->fetchSql($sql)
            ->update($data);
        return $num;
    }

    /**
     * 根据条件更新某个字段的值
     * @author 贺强
     * @time   2016-11-16 15:57:17
     * @param string $field 要更新的字段
     * @param string $value 要更新的字段的值
     * @param array|string $where 更新条件 形如：[['name','=','think'],['id','>',3], ……]或者 SQL 原生字符串，默认更新所有
     * @param  boolean $sql   是否打印 SQL
     * @return int 返回影响数据的条数，没修改任何数据字段返回 0
     */
    public function modifyField($field, $value, $where = '1=1', $sql = false)
    {
        $num = Db::table($this->table)
            ->where($where)
            ->fetchSql($sql)
            ->setField($field, $value);
        return $num;
    }

    /**
     * @author 贺强
     * @time   2016-11-16 15:57:17
     * 根据条件自增一个字段的值，可以延迟更新
     * @param string $field 要自增的字段
     * @param array|string $where 自增条件，形如：[['name','=','think'],['id','>',3], ……]或者 SQL 原生字符串，默认更新所有
     * @param int $value 要自增的值，默认为 1
     * @param int $delay 要延迟更新的时候，单位妙，默认 0 不延迟
     * @param  boolean $sql   是否打印 SQL
     * @return int 返回影响数据的条数
     */
    public function increment($field, $where = '1=1', $value = 1, $delay = 0, $sql = false)
    {
        $num = Db::table($this->table)
            ->where($where)
            ->fetchSql($sql)
            ->setInc($field, $value, $delay);
        return $num;
    }

    /**
     * 根据条件自减一个字段的值，可以延迟更新
     * @author 贺强
     * @time   2016-11-16 15:57:17
     * @param string $field 要自减的字段
     * @param array $where 自减条件，形如：[['name','=','think'],['id','>',3], ……]或者 SQL 原生字符串，默认更新所有
     * @param int $value 要自减的值，默认为 1
     * @param int $delay 要延迟更新的时候，单位妙，默认 0 不延迟
     * @param boolean $sql   是否打印 SQL
     * @return int 返回影响数据的条数
     */
    public function decrement($field, $where = '1=1', $value = 1, $delay = 0, $sql = false)
    {
        $num = Db::table($this->table)
            ->where($where)
            ->fetchSql($sql)
            ->setDec($field, $value, $delay);
        return $num;
    }

    /**
     * 根据条件获取总数量
     * @author 贺强
     * @time   2016-11-16 15:57:17
     * @param array|string $where 查询条件，形如：[['name','=','think'],['id','>',3], ……]或者 SQL 原生字符串，默认为空
     * @param string $group 对结果集进行分组，只能使用一个字符串，即字段名
     * @param boolean $sql   是否打印 SQL
     * @return int 返回总数量
     */
    public function getCount($where = null, $group = null, $sql = false)
    {
        $count = Db::table($this->table)
            ->group($group)
            ->where($where)
            ->fetchSql($sql)
            ->count();
        return $count;
    }

    /**
     * 根据条件查询一条数据
     * @author 贺强
     * @time   2016-11-16 15:57:17
     * @param array|string $where 查询条件，形如：[['name','=','think'],['id','>',3], ……]或者 SQL 原生字符串，默认为空
     * @param array|string $field 要查询的字段，形如：['id', 'name', ……]或者'id, name, ……'，默认显式的获取数据表的所有字段列表
     * @param array|string $order 按某(些)字段排序，形如：['order','id'=>'desc']或者'order, id desc'，默认按数据库中原顺序
     * @param boolean $remove 是否是排除某字段，如果为true则field必须是要排除的字段名
     * @param array|string $whereOr 查询 or 条件，形如：[['name','=','think'],['id','>',3], ……]或者 SQL 原生字符串，默认为空
     * @param boolean|string $lock 用于数据库的锁机制
     * @param boolean $sql   是否打印 SQL
     * @return array 返回查询结果(数组)，若不存在返回 null
     */
    public function getModel($where = null, $field = true, $order = '', $remove = false, $whereOr = null, $lock = false, $sql = false)
    {
        $model = Db::table($this->table)
            ->field($field, $remove)
            ->order($order)
            ->where($where)
            ->whereOr($whereOr)
            ->lock($lock)
            ->fetchSql($sql)
            ->find();
        return $model;
    }

    /**
     * 根据条件查询数据集
     * @author 贺强
     * @time   2016-11-16 15:57:17
     * @param array|string $where 查询条件，形如：[['name','=','think'],['id','>',3], ……]或者 SQL 原生字符串，默认为空
     * @param array|string $field 要查询的字段，形如：['id', 'name', ……]或者'id, name, ……'，默认显式的获取数据表的所有字段列表
     * @param string $page 分页查询，形如：'1, 10'，其中字符串中第一个数字表示第几页，第二个数字表示每页多少条
     * @param array|string $order 按某(些)字段排序，形如：['order','id'=>'desc']或者'order, id desc'，默认按数据库中原顺序
     * @param string $group 对结果集进行分组，多个字段以半角逗号隔开
     * @param array|string $whereOr 查询 or 条件，形如：[['name','=','think'],['id','>',3], ……]
     * @param boolean $remove 是否是排除某字段，如果为true则field必须是要排除的字段名
     * @param string $having 用于配合group方法完成从分组的结果中筛选(通常是聚合条件)数据，比如：'count(name)>0'
     * @param  boolean $sql   是否打印 SQL
     * @return array 返回查询结果(数组)，若不存在返回 null
     */
    public function getList($where = null, $field = true, $page = null, $order = '', $group = null, $whereOr = null, $remove = false, $having = null, $sql = false)
    {
        $list = Db::table($this->table)
            ->field($field, $remove)
            ->order($order)
            ->page($page)
            ->group($group)
            ->having($having)
            ->where($where)
            ->fetchSql($sql)
            ->select();
        return $list;
    }

    /**
     * 根据条件获取最大值
     * @author 贺强
     * @time   2019-01-15 18:21:44
     * @param  string $field 要获取的最大值字段
     * @param  string|array $where 查询条件，形如：[['name','=','think'],['id','>',3], ……]或原生 SQL 语句
     * @param  boolean $force 是否强制转换为数字
     * @param  boolean $sql   是否打印 SQL
     * @return int 返回查询结果
     */
    public function getMax($field, $where = null, $force = true, $sql = false)
    {
        $max = Db::table($this->table)
            ->where($where)
            ->fetchSql($sql)
            ->max($field, $force);
        return $max;
    }

    /**
     * 根据条件获取最小值
     * @author 贺强
     * @time   2019-01-15 18:29:10
     * @param  string  $field 要获取最小值的字段
     * @param  string|array $where 查询条件，形如：[['name','=','think'],['id','>',3], ……]或原生 SQL 语句
     * @param  boolean $force 是否强制转换为数字
     * @param  boolean $sql   是否打印 SQL
     * @return 返回查询结果
     */
    public function getMin($field, $where = null, $force = true, $sql = false)
    {
        $min = Db::table($this->table)
            ->where($where)
            ->fetchSql($sql)
            ->min($field, $force);
        return $min;
    }

    /**
     * 根据条件获取平均值
     * @author 贺强
     * @time   2019-01-15 18:34:58
     * @param  string  $field 要获取平均值的字段
     * @param  string  $where 查询条件，形如：[['name','=','think'],['id','>',3], ……]或原生 SQL 语句
     * @param  boolean $sql   是否打印 SQL
     * @return 返回查询结果
     */
    public function getAvg($field, $where = null, $sql = false)
    {
        $avg = Db::table($this->table)
            ->where($where)
            ->fetchSql($sql)
            ->avg($field);
        return $avg;
    }

    /**
     * 根据条件查询数据集
     * @author 贺强
     * @time   2016-11-16 15:57:17
     * @param array|string $where 查询条件，形如：['name'=>'think', 'id'=>['>', 3], ……]或者 SQL 原生字符串，默认为空
     * @param array|string $field 要查询的字段，形如：['id', 'name', ……]或者'id, name, ……'，默认显式的获取数据表的所有字段列表
     * @param string $limit 分页查询，形如：'1, 10'，其中字符串中第一个数字表示从第几条开始，第二个数字表示查询多少条
     * @param array|string $order 按某(些)字段排序，形如：['order','id'=>'desc']或者'order, id desc'，默认按数据库中原顺序
     * @param string $group 对结果集进行分组，只能使用一个字符串，即字段名
     * @param array|string $whereOr 查询 or 条件，形如：[['name','=','think'],['id','>',3], ……]
     * @param boolean $remove 是否是排除某字段，如果为true则field必须是要排除的字段名
     * @param string $having 用于配合group方法完成从分组的结果中筛选(通常是聚合条件)数据，比如：'count(name)>0'
     * @param  boolean $sql   是否打印 SQL
     * @return array 返回查询结果(数组)，若不存在返回 null
     */
    public function getLimitList($where = null, $field = true, $limit = null, $order = 'id', $group = null, $whereOr = null, $remove = false, $having = null, $sql = false)
    {
        $list = Db::table($this->table)
            ->field($field, $remove)
            ->order($order)
            ->limit($limit)
            ->group($group)
            ->having($having)
            ->where($where)
            ->where($whereOr)
            ->fetchSql($sql)
            ->select();
        return $list;
    }

    /**
     * 联接查询总数
     * @author 贺强
     * @time   2016-11-16 15:57:17
     * @param array $join 联表查询，形如：[['think_card c','a.card_id=c.id'], ……]
     * @param where array|string $where 查询条件，形如：[['name','=','think'],['id','>',3], ……]或者 SQL 原生字符串，默认为空
     * @param  boolean $sql   是否打印 SQL
     * @param string $alias 用于设置当前数据表的别名，默认别名为 a
     * @return int 返回总数量
     */
    public function getJoinCount($join, $where = null, $sql = false, $alias = 'a')
    {
        $count = Db::table($this->table)
            ->alias($alias)
            ->join($join)
            ->where($where)
            ->fetchSql($sql)
            ->count();
        return $count;
    }

    /**
     * 联表查询
     * @author 贺强
     * @time   2016-11-16 15:57:17
     * @param array $join 联表查询，形如：[['think_card c','a.card_id=c.id'], ……]
     * @param array|string $where 查询条件，形如：[['name','=','think'],['id','>',3], ……]或者 SQL 原生字符串，默认为空
     * @param array|string $field 要查询的字段，形如：['id', 'name', ……]或者'id, name, ……'，默认显式的获取数据表的所有字段列表
     * @param string $page 分页查询，形如：'1, 10'，其中字符串中第一个数字表示第几页，第二个数字表示每页多少条
     * @param boolean $remove 是否是排除某字段，如果为true则field必须是要排除的字段名
     * @param  boolean $sql   是否打印 SQL
     * @param string $alias 用于设置当前数据表的别名，默认别名为 a
     * @return array 返回查询结果(数组)，若不存在返回 null
     */
    public function getJoinList($join, $where = null, $field = true, $page = null, $order = null, $remove = false, $sql = false, $alias = 'a')
    {
        $list = Db::table($this->table)
            ->alias($alias)
            ->field($field)
            ->join($join)
            ->where($where)
            ->order($order)
            ->page($page)
            ->fetchSql($sql)
            ->select();
        return $list;
    }

    /**
     * UNION 查询
     * @author 贺强
     * @time   2016-11-16 15:57:17
     * @param  array   $union  联合的 SQL 语句，形如：['SELECT name FROM table1','SELECT name FROM table2']，每个union方法相当于一个独立的SELECT语句
     * @param  array|string  $where  查询条件，形如：[['name','=','think'],['id','>',3], ……]或者 SQL 原生字符串，默认为空
     * @param  array|string $field  要查询的字段，形如：['id', 'name', ……]或者'id, name, ……'，默认显式的获取数据表的所有字段列表
     * @param  boolean $is_all 是否是 UNION ALL 操作
     * @param  boolean $sql   是否打印 SQL
     * @param  string  $alias  用于设置当前数据表的别名，默认别名为 a
     * @return array 返回查询结果(数组)，若不存在返回 null
     */
    public function getUnionList($union, $where = null, $field = true, $is_all = false, $sql = false, $alias = 'a')
    {
        $list = Db::table($this->table)
            ->alias($alias)
            ->field($field)
            ->where($where)
            ->union($union, $is_all)
            ->fetchSql($sql)
            ->select();
        return $list;
    }

    /**
     * 执行原生 SQL 查询
     * @author 贺强
     * @time   2016-11-16 15:55:46
     * @param  string $sql 要执行的 SQL 语句
     * @param  array  $param 参数绑定，sql 语句中的 where 条件形如：where id=? and name=?或where id=:id and name=:name，则 param 格式为 [1,'think'] 或 [id=>1,name='think']
     * @return array       返回查询结果
     */
    public function query($sql, $param = null)
    {
        return Db::query($sql, $param);
    }

    /**
     * 用于更新和写入数据的sql操作
     * @author 贺强
     * @time   2019-01-15 18:09:10
     * @param  string $sql   要执行的 SQL 语句
     * @param  array  $param 参数绑定，sql 语句中的 where 条件形如：where id=? and name=?或where id=:id and name=:name，则 param 格式为 [1,'think'] 或 [id=>1,name='think']
     * @return int         返回影响的行数
     */
    public function execute($sql, $param = null)
    {
        return Db::execute($sql, $param);
    }
}
