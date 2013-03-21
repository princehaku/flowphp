<?php

/**
 * Request 请求重构类
 * 加强安全性和易用性
 *
 * @author princehaku
 * @site http://3haku.net
 */
class F_Request_Request {

    private $request;

    function __construct() {

        $this->request = $_GET;

        $this->request = array_merge($_POST, $this->request);

        if (Flow::$cfg['UNSET_REQS']) {
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
        if (!isset($this->request[$param])) {
            return $nullvalue;
        }
        $s = $this->request[$param];
        return $s;
    }

    /**
     * 得到安全的html
     *
     */
    public function getSafeHtml($param, $nullvalue = null) {
        if (!isset($this->request[$param])) {
            return $nullvalue;
        }
        $s = $this->request[$param];
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
        if (!isset($this->request[$param])) {
            return $nullvalue;
        }
        return intval($this->request[$param]);
    }

    /** 得到浮点型
     *
     *
     */
    public function getFloat($param, $nullvalue = null) {
        if (!isset($this->request[$param])) {
            return $nullvalue;
        }
        return (float)$this->request[$param];
    }

}
