<?php

/**
 * 数据库模型
 * 提供orm的部分支持
 *
 * @author princehaku
 * @site http://3haku.net
 */

class Module {
    /** 在数据库中的表名
     * @var array
     */

    protected $tableName = "";

    /** 在数据库的字段名
     * @var array
     * @depress
     */
    protected $fields = array();

    /** 查询出来的结果数据
     *
     * @var int
     */
    protected $rows = 0;

    /** 旧的数据
     *
     * @var unknown_type
     */
    protected $sourcedata = array();

    /** 新的数据
     *
     * @var unknown_type
     */
    protected $newdata = array();

    /**
     *
     * @var unknown_type
     */
    protected $tmpdata = array();

    /** 受到改变的索引值
     *
     */
    protected $effectedidx = array();

    /** 当前指针
     * 下标从0开始 如果没有数据 下标是-1
     * @var int
     */
    protected $idx = -1;

    /** sql中的where语句
     *
     */
    protected $queryWhere = "";

    /** sql中的Order语句
     *
     */
    protected $queryOrder = "";

    /** sql中的limit语句
     *
     */
    protected $queryLimit = "";

    public function __construct($name) {
        if ($this->tableName == "")
            $this->tableName = C("DB_PREFIX") . $name;

        //$this->fields=DB()->getFields($this->tableName);
    }

    /** 魔术函数 实现直接控制属性
     *
     * @param $key
     * @return $value
     */

    public function __get($key) {
        $res = $this->newdata[$this->idx][$key];
        return $res;
    }

    /** 得到属性
     *
     * @param $key
     * @return $value
     */

    public function get($key) {
        return self::__get($key);
    }

    /** 魔术函数 实现直接设置属性
     *
     * @param $key
     * @return $this
     */

    public function __set($key, $value) {
        if ($this->idx != -1) {
            //如果执行过查询 放newdata里面
            $this->newdata[$this->idx][$key] = $value;
            //标记更改
            $isinarray = false;
            foreach ($this->effectedidx as $i => $k) {
                if ($k == $this->idx) {
                    $isinarray = true;
                }
            }
            if (!$isinarray) {
                array_push($this->effectedidx, $this->idx);
            }
        } else {
            //如果没有执行过查询 放tmpdata里面
            $this->tmpdata[$key] = $value;
        }
        return $this;
    }

    /** 设置属性
     *
     * @param $key
     * @param $value
     * @return $this
     */

    public function set($key, $value) {
        return self::__set($key, $value);
    }

    /** 查询所有的字段
     * 用find函数实现的
     *
     * @return $this
     */

    public function findall() {
        try {
            $this->find("*");
        } catch (FlowException $ex) {
            throw $ex;
        }
        return $this;
    }

    /** 查询某字段
     *
     * @return $this
     */

    public function find($colsname) {
        $where = $this->queryWhere == "" ? "" : " where " . $this->queryWhere;
        $limit = $this->queryLimit == "" ? "" : " limit " . $this->queryLimit;
        $order = $this->queryOrder == "" ? "" : " order by " . $this->queryOrder;
        try {
            $res = D()->sql("select $colsname from `" . $this->tableName . "`" . $where . $order . $limit);
        } catch (FlowException $ex) {
            Flow::Log()->e("Module `$this->tableName`: 查询数据失败  " . $ex->getMessage());
            throw new FlowException("Module `$this->tableName`: 查询数据失败  " . $ex->getMessage());
        }
        $this->sourcedata = $res;
        $this->newdata = $res;
        $this->rows = count($res);
        if ($this->rows == 0) {
            Flow::Log()->w("Module `$this->tableName`: 没有记录返回");
            $this->reset();
        }
        //如果大于一行  idx等于0
        if ($this->rows >= 1) {
            $this->idx = 0;
        }
        return $this;
    }

    public function toArray() {
        return $this->newdata;
    }

    /** 上次查询的所有记录总数
     *
     * @return $this
     */

    public function size() {
        return isset($this->rows) ? $this->rows : 0;
    }

    /** 得到某行数据
     * 原理是把指针设置到某行
     * @param $idx
     * @return $this
     */

    public function getrow($idx) {
        if ($idx >= $this->rows) {
            Flow::Log()->e("Module `$this->tableName`: $idx行 不存在 ");
            throw new FlowException("Module `$this->tableName`: $idx行 不存在 ");
            return $this;
        }
        $this->idx = $idx;
        return $this;
    }

    /** sql where语句
     *
     * @param $idx
     * @return $this
     */

    public function where($whereString) {
        if (is_array($whereString)) {
            $whereString = $this->buildRUDWhere($whereString);
        }
        $this->queryWhere = $whereString;
        return $this;
    }

    /** sql order语句
     *
     * @param $idx
     * @return $this
     */

    public function order($orderString) {

        $this->queryOrder = $orderString;

        return $this;
    }

    /** sql limit语句
     *
     * @param $idx
     * @return $this
     */

    public function limit($limit1, $limit2 = "") {
        if ($limit2 != "") {
            $this->queryLimit = $limit1 . "," . $limit2;
        } else {
            $this->queryLimit = $limit1;
        }
        return $this;
    }

    /** 构造用于更新和删除的where语句块
     *
     * @param $data
     * @return string
     */

    private function buildRUDWhere($data) {
        //读取和处理where语句
        $oldwhere = trim($this->queryWhere);
        $where = "";
        $total = count($data);
        $r = 0;
        foreach ($data as $i => $j) {
            $r++;
            $where .= "`$i`='$j'";
            if ($r != $total)
                $where .= " AND ";
        }
        //组合上旧的where语句
        if ($oldwhere != "") {
            $where == "" ? $where .= " " . $oldwhere : $where .= " AND " . $oldwhere;
        }
        return $where;
    }
    /** 构造用于更新的set语句块
     *
     * @param $data
     * @return string
     */

    private function buildUSet($data) {
        $set = "";
        $total = count($data);
        $r = 0;
        foreach ($data as $i => $j) {
            $r++;
            $set .= "`$i`='$j'";
            if ($r != $total)
                $set .= ",";
        }
        return $set;
    }

    /** 构造用于插入的insert语句块
     *
     * @param $data
     * @return sting
     */

    private function buildCI($data) {
        $set = "";
        $total = count($data);
        $rkey = "";
        $rvalue = "";
        $r = 0;
        foreach ($data as $i => $j) {
            $r++;
            $rkey .= "`$i`";
            $rvalue .= "'$j'";
            if ($r != $total) {
                $rkey .= ",";
                $rvalue .= ",";
            }
        }
        $set = " ($rkey) values ($rvalue)";

        return $set;
    }

    /** 将有经过set操作的改动更新到数据库
     *
     * @return $this
     */

    public function update() {

        $limit = $this->queryLimit == "" ? "" : " limit " . $this->queryLimit;
        $order = $this->queryOrder == "" ? "" : " order by " . $this->queryOrder;

        foreach ($this->effectedidx as $i => $j) {
            try {
                D()->sql("update `$this->tableName`  set " . $this->buildUSet($this->newdata[$j]) . " where " . $this->buildRUDWhere($this->sourcedata[$j]) . $order . $limit);
                $this->sourcedata[$j] = $this->newdata[$j];
            } catch (FlowException $ex) {
                Flow::Log()->e("Module `$this->tableName`: 更新数据失败  " . $ex->getMessage());
                throw new FlowException("Module `$this->tableName`: 更新数据失败  " . $ex->getMessage());
            }
        }
        Flow::Log()->i("Module `$this->tableName`: 更新数据" . count($this->effectedidx) . "行");
        //更新旧数据
        $this->effectedidx = array();
        $this->tmpdata = array();
        return $this;
    }

    /** 将数据删除
     * 临时数据清空
     * 某单行数据清空
     * @return $this
     */

    public function delete() {

        $limit = $this->queryLimit == "" ? "" : " limit " . $this->queryLimit;
        $order = $this->queryOrder == "" ? "" : " order by " . $this->queryOrder;

        if ($this->idx != -1) {

            try {
                D()->sql("delete from `$this->tableName` where " . $this->buildRUDWhere($this->sourcedata[$this->idx])) . $order . $limit;
            } catch (FlowException $ex) {
                Flow::Log()->e("Module `$this->tableName`: 删除数据失败  " . $ex->getMessage());
                throw new FlowException("Module `$this->tableName`: 删除数据失败  " . $ex->getMessage());
            }
            $this->sourcedata[$this->idx] = null;
            $this->newdata[$this->idx] = null;
        }

        Flow::Log()->i("Module `$this->tableName`: 删除数据 位于$this->idx");

        $this->tmpdata = array();

        return $this;
    }

    /** 保存数据
     * 自动选择update或者insert
     *
     * @return $this
     */

    public function save() {
        if (0 == count($this->tmpdata)) {
            self::update();
        } else {
            self::insert();
        }
        return $this;
    }

    /** 清空状态 新数据作为新行加入
     *
     * @return $this
     */

    public function newRow() {
        $this->tmpdata = null;
        return $this;
    }

    /** 将数据插入到数据库
     * 注意:插入后的数据作为旧数据
     * 临时数据清空
     * @return $this
     */

    public function insert() {

        if ($this->tmpdata == null) {
            return $this;
        }

        try {
            D()->sql("insert into `$this->tableName` " . $this->buildCI($this->tmpdata));
        } catch (FlowException $ex) {
            Flow::Log()->e("Module `$this->tableName`: 插入数据失败  " . $ex->getMessage());
            throw new FlowException("Module `$this->tableName`: 插入数据失败  " . $ex->getMessage());
        }

        $this->sourcedata[0] = $this->tmpdata;

        $this->newdata[0] = $this->tmpdata;

        $this->idx = 0;

        $this->tmpdata = array();

        return $this;
    }

    /** 得到上次成功插入后的id
     *
     */

    public function getLastInsertID() {

        return D()->getLastInsertId();
    }

    /** 得到上次执行的sql语句
     *
     */

    public function getLastQuery() {

        return D()->getLastQuery();
    }

    /** 将查询到的数据全部删除
     * 注意:会清空所有状态
     * @return $this
     */

    public function delall() {

        $where = $this->queryWhere == "" ? "" : " where " . $this->queryWhere;

        $limit = $this->queryLimit == "" ? "" : " limit " . $this->queryLimit;

        try {
            D()->sql("delete from `$this->tableName` " . $where . $limit);
        } catch (FlowException $ex) {
            Flow::Log()->e("Module `$this->tableName`: 删除数据失败  " . $ex->getMessage());
            throw new FlowException("Module `$this->tableName`: 删除数据失败  " . $ex->getMessage());
        }
        $this->resetData();

        return $this;
    }

    /** 重置数据状态
     *
     * return $this
     */

    public function resetData() {
        $this->sourcedata = array();
        $this->newdata = array();
        $this->tmpdata = array();
        $this->effectedidx = array();
        return $this;
    }

    /** 重置sql语句状态
     *
     */

    public function resetQuery() {
        $this->queryWhere = "";
        $this->queryLimit = "";
        $this->queryOrder = "";
        $this->rows = 0;
        $this->idx = -1;
        return $this;
    }

    /** 重置所有数据和sql状态
     *
     */

    public function reset() {
        $this->resetData();
        $this->resetQuery();
        return $this;
    }

}
