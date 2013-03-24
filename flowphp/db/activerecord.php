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

    public function where() {

    }

    public function findall() {

    }

    public function save($record, $table_name = null) {

        if (null != $table_name) {
            $this->_fetchTableInfo($table_name);
        }

        if (is_object($record)) {
            $record = (array)$record;
        }
        $query_where = " where 1==1 and ";
        foreach ($record as $key => $value) {
            $value = addslashes($value);
            if ($this->tableinfo[$key]['Key'] == 'PRI') {
                $query_where .= "`$key` = '$value'";
            }

        }

        echo $query_where;
    }

    public function _fetchTableInfo($table_name) {
        $cache_dir = Flow::$cfg["appcache_dir"] . '/db/';
        $cache_path = $cache_dir . $this->dbname . "_" . $table_name . ".php";
        if (file_exists($cache_path) && !DEV_MODE) {
            return include $cache_path;
        }
        $dbh = $this->dbh;

        $ps = $dbh->query("desc $table_name");
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
        preg_match("/from (.*?) /i", $sql, $matched);
        $table_name = trim($matched[1], "`");
        $table_info = $this->_fetchTableInfo($table_name);
        $this->tableinfo = $table_info;
    }
}