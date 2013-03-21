<?php

/** 数据库引擎 提供数据库的CRUD操作
 *
 * @author princehaku
 * @site http://3haku.net
 */

class DB {
    /** sql结构集
     * @access private
     */

    private $sqlResultSet;

    /** 数据库主机
     *
     * @var mixed
     */
    private $dbhost;

    /** 数据库名字
     *
     * @var mixed
     */
    private $dbname;

    /**
     * 数据库用户名
     *
     * @var mixed
     */
    private $dbuser;

    /**
     * 数据库密码
     *
     * @var mixed
     */
    private $dbpwd;

    /**
     * 数据库连接编码
     *
     * @var mixed
     */
    private $dbcharset = "utf8";

    /** 上次sql语句
     *
     */
    private $lastQuery = "";

    /** 构造函数
     *
     * @param array $config
     * @access pubic
     */

    public function __construct($config = null) {
        //定义新的数据库库连接串 如果没有 使用默认值
        $this->dbhost = $config["DB_HOST"] == null ? C("DB_HOST") : $config["DB_HOST"];
        $this->dbname = $config["DB_NAME"] == null ? C("DB_NAME") : $config["DB_NAME"];
        $this->dbuser = $config["DB_USER"] == null ? C("DB_USER") : $config["DB_USER"];
        $this->dbpwd = $config["DB_PASSWORD"] == null ? C("DB_PASSWORD") : $config["DB_PASSWORD"];
        $this->dbcharset = $config["DB_CHARSET"] == null ? C("DB_CHARSET") : $config["DB_CHARSET"];
        if ($this->dbcharset == "") {
            Flow::Log()->w("使用默认utf-8编码连接数据库");
            $this->dbcharset = "utf8";
        }
        $this->connect();
    }

    /** 析构函数 关闭sql连接
     *
     * @access pubic
     */

    public function __destruct() {

        if (null != $this->_identifyId) {
            mysql_close($this->_identifyId);
        }
    }

    /** 连接数据库
     * @access private
     */

    private function connect() {

        if (!isset($this->_identifyId)) {
            //是否创建持久性连接
            if (C("DB_PERSISTANT") == 1) {
                $this->_identifyId = mysql_pconnect($this->dbhost, $this->dbuser, $this->dbpwd, true);
            } else {
                $this->_identifyId = mysql_connect($this->dbhost, $this->dbuser, $this->dbpwd, true);
            }

            if (null == $this->_identifyId) {
                throw new FlowException("连接失败" . $this->dbhost . " Info:" . mysql_error());
            }
            if (!mysql_select_db($this->dbname, $this->_identifyId)) {
                throw new FlowException("连接失败 DBName: " . $this->dbname . " Info:" . mysql_error($this->_identifyId));
            }
            mysql_query("set names " . $this->dbcharset, $this->_identifyId);

            Flow::Log()->i("DB " . $this->dbname . " Connected");
        }
    }

    /** 万能sql语句  如果是查询数据成功 返回数据数组 失败返回null
     * 其他数据操作执行成功返回true 失败返回false
     * @param  $sql sql语句
     * @access pubic
     */

    public function sql($sql) {
        if ($this->_identifyId == null) {
            throw new FlowException("连接失败");
        }
        //清空结果集
        $this->sqlResultSet = null;

        $result = array();

        $q = $this->query($sql, $this->_identifyId);

        $i = 0;
        //如果是bool 值 返回bool
        if (is_bool($q)) {
            return $q;
        }
        //遍历结果 重组成新数组
        while (null != ($res = mysql_fetch_assoc($q))) {
            $result[$i++] = $res;
        }
        $this->result = $result;

        if (0 !== count($result)) {
            return $result;
        } else {
            return array();
        }
    }

    /**
     * 取得数据表的字段信息
     *
     */
    public function getFields($tableName) {
        $result = $this->sql("show columns from `$tableName`");
        $info = array();
        if ($result) {
            foreach ($result as $key => $val) {
                $info[] = $val['Field'];
            }
        }

        return $info;
    }

    /**
     * 取得数据的所有表
     * 返回格式 0=>xxx 1=>xxxx
     */
    public function getTables() {
        $result = $this->sql("SHOW TABLES");
        $res = array();
        foreach ($result as $i => $j) {
            $vu = "";
            foreach ($j as $k => $v) {
                $vu = $v;
            }
            $res[$i] = $vu;
        }
        return $res;
    }

    /**
     * 执行sql语句
     * @return null | resultset
     * @param sqlstatment $sql
     */

    private function query($sql) {

        if (null !== $this->sqlResultSet) {
            return $this->sqlResultSet;
        }

        $querytime_before = array_sum(explode(' ', microtime()));

        $this->lastQuery = $sql;

        $this->sqlResultSet = mysql_query($sql, $this->_identifyId);

        $querytime_after = array_sum(explode(' ', microtime()));

        if (!$this->sqlResultSet) {
            Flow::Log()->e("sql执行失败 ：$sql  :" . mysql_error($this->_identifyId));
            throw new FlowException("sql_error" . " ：$sql  :" . mysql_error($this->_identifyId));
        } else {
            Flow::Log()->i("执行语句  :  $sql 执行时间 :" . ($querytime_after - $querytime_before));
        }

        return $this->sqlResultSet;
    }

    /** 取得上一步 INSERT 操作产生的 ID
     * 此ID为主键
     *
     */

    public function getLastInsertId() {
        return mysql_insert_id($this->_identifyId);
    }

    /**
     * 得到上次操作的sql语句
     *
     */
    public function getLastQuery() {
        return $this->lastQuery;
    }

    /**
     * 得到上次操作影响的列数
     *
     */
    public function getAffectedRows() {
        return mysql_affected_rows();
    }

    /** 从 上次SQL 操作结果集中取得列信息
     *
     */
    public function getLastFields() {

        $this->sqlResultSet = null;

        $fields = array();

        $this->query($this->getLastQuery());

        while (null != ($property = mysql_fetch_field($this->sqlResultSet))) {
            $fields[] = $property->name;
        }
        return $fields;
    }

}