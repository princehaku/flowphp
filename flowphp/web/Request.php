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

    private $_params;

    /**
     * 安全过滤后的参数们
     * @var
     */
    private $_safeParams;

    /**
     * 安全的Uri
     *
     * @var
     */
    private $_safeUri;

    /**
     * 安全的Url
     *
     * @var
     */
    private $_safeUrl;

    public function init() {

        $this->_params = $_GET;

        $this->_params = array_merge($_POST, $this->_params);

        $this->_safeParams = F_Helper_Array::htmlspecialchars($this->_params);

        if (isset(Flow::$cfg['safe_input']) && Flow::$cfg['safe_input']) {
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
        if (!isset($this->_params[$param])) {
            return $nullvalue;
        }
        $s = $this->_safeParams[$param];
        return $s;
    }

    /**
     * 得到安全的html
     * 会过滤掉一些危险的html的标记
     */
    public function getSafeHtml($param, $nullvalue = null) {
        if (!isset($this->_params[$param])) {
            return $nullvalue;
        }
        $s = $this->_params[$param];
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
        if (!isset($this->_params[$param])) {
            return $nullvalue;
        }
        return intval($this->_params[$param]);
    }

    /**
     * 得到浮点型
     *
     */
    public function getFloat($param, $nullvalue = null) {
        if (!isset($this->_params[$param])) {
            return $nullvalue;
        }
        return (float)$this->_params[$param];
    }

    /**
     * 得到原始的串，会有XSS问题
     *
     */
    public function getRaw($param, $nullvalue = null) {
        if (!isset($this->_params[$param])) {
            return $nullvalue;
        }
        return $this->_params[$param];
    }

    /**
     * 安全的BaseUri
     * 通过get参数内的重新拼接
     *
     */
    public function getBaseUri() {
        if ($this->_safeUri == null) {
            $ps = strpos($_SERVER['REQUEST_URI'], '?');
            if ($ps === false) {
                $path = $ps;
            } else {
                $path = substr($_SERVER['REQUEST_URI'], 0, $ps);
            }
            $this->_safeUri = empty($this->_safeParams) ? $path :
                $path . '?' . http_build_query($this->_safeParams);
        }
        return $this->_safeUri;
    }

    /**
     * 安全的BaseUrl
     * 通过get参数内的重新拼接
     *
     */
    public function getBaseUrl() {
        if ($this->_safeUrl == null) {
            $uri = $this->getBaseUri();
            if ($_SERVER['SERVER_PORT'] == 80 || $_SERVER['SERVER_PORT'] == 433) {
                $port = '';
                $schema = 'http';
                if ($_SERVER['SERVER_PORT'] == 433) {
                    $schema = 'https';
                }
            } else {
                $port = ':' . $_SERVER['SERVER_PORT'];
                $schema = 'http';
            }
            $this->_safeUrl = $schema . '://' . $_SERVER['SERVER_NAME'] . $port . $uri;
        }
        return $this->_safeUrl;
    }
}
