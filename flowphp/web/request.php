<?php

/**
 * Request 请求重构类
 * 加强安全性和易用性
 * 用于web项目
 *
 * @author princehaku
 * @site http://3haku.net
 */
class F_Web_Request {

    private $params;

    function __construct() {

        $this->params = $_GET;

        $this->params = array_merge($_POST, $this->params);

        if (Flow::$cfg['unset_reqs']) {
            unset($_GET);
            unset($_POST);
            unset($_REQUEST);
        }
    }

    /**
     * 转义所有  移除html代码
     * 转义html字符
     *
     * @see strip
     */
    public function getText($param, $nullvalue = null) {
        if (!isset($this->params[$param])) {
            return $nullvalue;
        }
        $s = $this->params[$param];
        return $s;
    }

    /**
     * 得到安全的html
     *
     */
    public function getSafeHtml($param, $nullvalue = null) {
        if (!isset($this->params[$param])) {
            return $nullvalue;
        }
        $s = $this->params[$param];
        $s = preg_replace(array(
            '/<(\/?)(script|i?frame|style|html|body|title|link|meta|\?|\%)([^>]*?)>/isU', "/(<[^>]*)on[a-zA-Z] \s*=([^>]*>)/isU"
        ), "", $s);
        return $s;
    }

    /**
     * 得到整型
     *
     */
    public function getInt($param, $nullvalue = null) {
        if (!isset($this->params[$param])) {
            return $nullvalue;
        }
        return intval($this->params[$param]);
    }

    /** 得到浮点型
     *
     *
     */
    public function getFloat($param, $nullvalue = null) {
        if (!isset($this->params[$param])) {
            return $nullvalue;
        }
        return (float)$this->params[$param];
    }

}
