<?php

/**
 * 数据库模型
 * 提供AR支持
 *
 * @author princehaku
 * @site http://3haku.net
 */

class F_DB_ConnectionManager {

    public $connectionString = "";

    public $username;

    public $password;

    public $charset;
    /**
     * res指针
     *
     * @var PDO
     */
    protected $dbh;

    protected $dbname;
    /**
     *
     */
    public function init() {
        $dbh = new PDO($this->connectionString, $this->username, $this->password);
        preg_match("/dbname=(.*?);+/", $this->connectionString, $matches);
        $this->dbname = $matches[1];
        $this->dbh = $dbh;
        if (!empty($this->charset)) {
            $this->query("set names " . $this->charset);
        }
    }
    /**
     * 执行一条sql语句
     * 返回数组或者bool
     *
     * @param $sql
     * @return array|bool
     */
    public function query($sql) {
        $dbh = $this->dbh;
        $this->_beforeQuery($sql);
        $ps = $dbh->query($sql);
        $error_info = $this->dbh->errorInfo();
        if ($error_info[2] != null) {
            throw New Exception("执行SQL失败，原因: " . $error_info[2]);
        }
        if (is_bool($ps)) {
            return $ps;
        }
        $obj = $ps->fetchAll(PDO::FETCH_ASSOC);
        return $obj;
    }

    protected function _beforeQuery($sql) {
        Flow::Log()->debug("[SQL] " . $sql);
        return;
    }
    /**
     * 得到底层原始的pdo
     *
     * @return PDO
     */
    public function getPdoObject() {
        return $this->dbh;
    }

}
