<?php
/**
 * Copyright 2013 princehaku
 *
 *  FileName   : activerecord.php
 *  Created on : 13-3-24 , 下午5:12
 *  Author     : haku
 *  Blog       : http://3haku.net
 */
class F_DB_ActiveRecord extends F_DB_ConnectManager {

    public $tablename;

    public $tableinfo;

    private $_query_where = "";

    private $_query_order = "";

    private $_query_limit = "";

    public function where($where) {
        $this->_query_where = $where;
        return $this;

    }

    public function limit($offset = null, $size = null) {
        $this->_query_limit = " limit " . (int)$offset;
        if ($size != null) {
            $this->_query_limit .= "," . (int)$size;
        }
        return $this;
    }

    public function order($order) {
        $this->_query_order = " order by " . $order;
        return $this;
    }
    /**
     * 查询所有结果
     * @return array|bool
     */
    public function findall() {
        $res_arr = $this->query("select * from " . $this->tablename . $this->_query_where .
            $this->_query_order . $this->_query_limit);
        return $res_arr;
    }
    /**
     * 初始化 acl要求必须指定表名
     *
     * @return $this|void
     */
    public function init() {
        parent::init();
        $this->tableinfo = $this->_fetchTableInfo($this->tablename);
        return $this;
    }

    /**
     * 保存一跳记录
     * 要求和表的字段对应
     * @param $record
     * @return bool
     * @throws Exception
     */
    public function save($record) {

        if (is_object($record)) {
            $record = (array)$record;
        }
        $keys = array();
        $values = array();

        foreach ($record as $key => $value) {
            if (DEV_MODE && !is_string($value)) {
                throw new Exception("activeRecord的值必须是字符串类型");
            }
            // avoid sql inject
            $value = addslashes($value);
            if (empty($this->tableinfo[$key])) {
                throw new Exception("activeRecord的值{$key}必须对应到表的字段");
            }
            $keys[] = "`$key`";
            $values[] = "'$value'";
        }

        $sql = "insert into `" . $this->tablename . "` (" . implode(",", $keys) . ") values (" .
            implode(",", $values) . ")";

        return $this->query($sql);
    }

    public function update($record) {

        if (is_object($record)) {
            $record = (array)$record;
        }
        $query_where = " where 1=1 and ";
        $sets = array();
        $has_pk = false;

        foreach ($record as $key => $value) {
            if (DEV_MODE && !is_string($value)) {
                throw new Exception("activeRecord的值必须是字符串类型");
            }
            // avoid sql inject
            $value = addslashes($value);
            if (empty($this->tableinfo[$key])) {
                throw new Exception("activeRecord的值{$key}必须对应到表的字段");
            }
            if (strpos($this->tableinfo[$key]['Key'], 'PRI') !== false) {
                $has_pk = true;
                $query_where .= "`$key` = '$value'";
            }
            $sets[] = "`$key` = '$value'";
        }

        if (!$has_pk) {
            throw new Exception("要保存它，必须存在对应的主键");
        }

        $sql = "update " . $this->tablename . " set " . implode(",", $sets) . $query_where;

        return $this->query($sql);
    }

    public function _fetchTableInfo($table_name) {
        $cache_dir = Flow::$cfg["appcache_dir"] . '/db/';
        $cache_path = $cache_dir . $this->dbname . "_" . $table_name . ".php";
        if (file_exists($cache_path) && !DEV_MODE) {
            return include $cache_path;
        }
        $dbh = $this->dbh;

        $ps = $dbh->query("desc $table_name");
        if ($ps == false) {
            throw new Exception("不存在的表 $table_name");
        }
        $res_arr = $ps->fetchAll(PDO::FETCH_ASSOC);
        $col_infos = array();
        foreach ($res_arr as $res) {
            $col_infos[$res['Field']] = $res;
            if ($res['Key'] == 'PRI') {
                //die($res['Field']);
            }
        }
        // 检测缓存文件夹是否存在
        if (!file_exists($cache_dir)) {
            if (!mkdir($cache_dir, 0777, 1)) {
                throw new Exception("缓存文件夹" . $cache_dir . "创建失败");
            }
        }
        file_put_contents($cache_path, var_export($col_infos, 1));
        return $col_infos;
    }

    protected function _beforeQuery($sql) {
        return;
    }
}