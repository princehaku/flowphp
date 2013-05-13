<?php
/**
 * Copyright 2013 princehaku
 *
 *  FileName   : armanager.php
 *  Created on : 13-3-24 , 下午5:12
 *  Author     : haku
 *  Blog       : http://3haku.net
 */
/**
 * Class F_DB_ARManager
 */
class F_DB_ARManager extends F_DB_ConnectionManager {

    public $tablename;

    public $tableinfo;

    public $lastSql;

    private $_query_where = "";

    private $_query_order = "";

    private $_query_limit = "";

    protected static $acm;
    /**
     * sql语句中的where字段
     *
     * @param $where
     * @return $this
     */
    public function where($where) {
        $this->_query_where = " where " . $where;
        return $this;
    }

    /**
     * sql语句中的limit字段
     * @param null $offset
     * @param null $size
     * @return $this
     */
    public function limit($offset = null, $size = null) {
        $this->_query_limit = " limit " . (int)$offset;
        if ($size != null) {
            $this->_query_limit .= "," . (int)$size;
        }
        return $this;
    }

    /**
     * sql语句中的order字段
     * @param $order
     * @return $this
     */
    public function order($order) {
        $this->_query_order = " order by " . $order;
        return $this;
    }
    /**
     * 查询所有字段的结果
     * @return array|bool
     */
    public function findall() {
        $sql = "select * from " . $this->tablename . $this->_query_where .
            $this->_query_order . $this->_query_limit;
        $this->_clearStatues();
        $res_arr = $this->query($sql);
        return $res_arr;
    }

    private function _clearStatues() {
        $this->_query_where =
        $this->_query_order = $this->_query_limit = "";
    }

    /**
     * 初始化 acl要求必须指定表名
     *
     * @return F_DB_ARManager
     */
    public function table($tablename, $new_instance = false) {
        if (empty(self::$acm[$tablename]) || $new_instance) {
            self::$acm[$tablename] = clone $this;
            self::$acm[$tablename]->tablename = $tablename;
            self::$acm[$tablename]->tableinfo = $this->_fetchTableInfo($tablename);
        }
        return self::$acm[$tablename];
    }

    public function __clone() {
    }
    /**
     * 保存一条记录
     * 要求和表的字段对应
     * @param $record
     * @return bool
     * @throws Exception
     */
    public function save($record) {

        if (empty($record)) {
            throw new Exception("需要保存的acriveRecord为空");
        }
        if (!is_array($record)) {
            $record = (array)$record;
        }
        $keys = array();
        $values = array();

        foreach ($record as $key => $value) {
            if (empty($value)) {
                $value = "";
            }
            if (is_bool($value)) {
                $value = $value . "";
            }
            if (DEV_MODE && !is_string($value)) {
                throw new Exception("activeRecord{$key}的值必须是字符串类型");
            }
            // avoid sql inject
            $value = addslashes($value);
            if (empty($this->tableinfo[$key])) {
                throw new Exception("activeRecord{$key}的值必须对应到表的字段");
            }
            $keys[] = "`$key`";
            $values[] = "'$value'";
        }

        $sql = "insert into `" . $this->tablename . "` (" . implode(",", $keys) . ") values (" .
            implode(",", $values) . ")";

        $query_status = $this->query($sql);
        return $query_status;
    }

    public function update($record) {

        if (is_object($record)) {
            $record = (array)$record;
        }
        $query_where = " where 1=1 and ";
        $sets = array();
        $has_pk = false;

        foreach ($record as $key => $value) {
            if (empty($value)) {
                $value = "";
            }
            if (is_bool($value)) {
                $value = $value . "";
            }
            if (DEV_MODE && !is_string($value)) {
                throw new Exception("activeRecord{$key}的值必须是字符串类型");
            }
            // avoid sql inject
            $value = addslashes($value);
            if (empty($this->tableinfo[$key])) {
                throw new Exception("activeRecord{$key}的值必须对应到表的字段");
            }
            if (strpos($this->tableinfo[$key]["PRI"], "PRI") !== false) {
                $has_pk = true;
                $query_where .= "`$key` = '$value'";
            }
            $sets[] = "`$key` = '$value'";
        }

        if (!$has_pk) {
            throw new Exception("要保存它，必须存在对应的主键");
        }

        $sql = "update " . $this->tablename . " set " . implode(",", $sets) . $query_where;
        $query_status = $this->query($sql);
        return $query_status;
    }

    public function _fetchTableInfo($table_name) {
        $f_cache = new F_Cache_File();
        $appcache_dir = isset(Flow::$cfg["appcache_dir"]) ? Flow::$cfg["appcache_dir"] : APP_PATH . "/appcache/";
        $f_cache->setBaseDir($appcache_dir);

        if (!DEV_MODE && null != $f_cache["db." . $this->dbname . "_" . $table_name]) {
            return $f_cache["db." . $this->dbname . "_" . $table_name];
        }
        $dbh = $this->dbh;

        $ps = $dbh->query("desc $table_name");
        if ($ps == false) {
            throw new Exception("不存在的表 $table_name");
        }
        $res_arr = $ps->fetchAll(PDO::FETCH_ASSOC);
        $col_infos = array();
        foreach ($res_arr as $res) {
            $col_infos[$res["Field"]] = $res;
            if ($res["Key"] == "PRI") {
                //die($res["Field"]);
            }
        }
        $f_cache["db." . $this->dbname . "_" . $table_name] = $col_infos;
        return $col_infos;
    }

    protected function _beforeQuery($sql) {
        parent::_beforeQuery($sql);
        $this->lastSql = $sql;
        return;
    }
}