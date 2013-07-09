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

    public function init() {

        $this->params = $_GET;

        $this->params = array_merge($_POST, $this->params);

        if (isset(Flow::$cfg['unset_reqs']) && Flow::$cfg['unset_reqs']) {
            unset($_GET);
            unset($_POST);
            unset($_REQUEST);
        }
    }

    /**
     * 转义所有代码
     * 使用strip_tags移除html代码
     * 使用htmlspecialchars转义html字符
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
     * 会过滤掉一些危险的html的标记
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
     * 得到整型的参数
     * 注意和限制和机器有关32位和64位MAX_INT_NUM不一样
     */
    public function getInt($param, $nullvalue = null) {
        if (!isset($this->params[$param])) {
            return $nullvalue;
        }
        return intval($this->params[$param]);
    }

    /**
     * 得到浮点型
     *
     */
    public function getFloat($param, $nullvalue = null) {
        if (!isset($this->params[$param])) {
            return $nullvalue;
        }
        return (float)$this->params[$param];
    }

    /**
     * 安全的BaseUri
     * 通过get参数内的重新拼接
     *
     */
    public function getBaseUri() {

    }

    /**
     * 安全的BaseUri
     * 通过get参数内的重新拼接
     *
     */
    public function getBaseUrl() {

    }
}
