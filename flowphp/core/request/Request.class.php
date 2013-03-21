<?php

/**
 * Request 请求重构类
 * 加强安全性和易用性
 *
 * @author princehaku
 * @site http://3haku.net
 */
class Request {

    private $request;
    private $hasquoted;

    function __construct() {

        $this->request = $_GET;

        $this->request = array_merge($_POST, $this->request);

        if (ini_get('register_globals') == 1) {
            $this->hasquoted = 1;
        } else {
            $this->hasquoted = 0;
        }
        if (C("FORCE_REQUEST")) {
            unset($_GET);
            unset($_POST);
            unset($_REQUEST);
        }
    }

    /** 转义预定义字符
     * 移除html标记
     */
    public function getString($param, $nullvalue = null) {
        if (!isset($this->request[$param])) {
            return $nullvalue;
        }
        $s = $this->request[$param];
        if (!$this->hasquoted) {
            $s = htmlspecialchars_deep($s);
        }
        return $s;
    }

    /** 原始输入
     */
    public function getRawString($param, $nullvalue = null) {
        if (!isset($this->request[$param])) {
            return $nullvalue;
        }
        $s = $this->request[$param];
        return $s;
    }

    /** 转义所有  移除html代码
     * 转义html字符
     *
     * @see strip
     */
    public function getText($param, $nullvalue = null) {
        if (!isset($this->request[$param])) {
            return $nullvalue;
        }
        $s = $this->request[$param];
        $s = addslashes_deep($s);
        return $s;
    }

    /** 得到安全的html值
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
        if (!$this->hasquoted)
            $s = addslashes_deep($s);
        return $s;
    }

    /** 得到整型
     *
     *
     */
    public function getInt($param, $nullvalue = null) {
        if (!isset($this->request[$param])) {
            return $nullvalue;
        }
        return (int)$this->request[$param];
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
