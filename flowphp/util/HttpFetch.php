<?php

/**
 * 用curl做的联网类
 * 支持cookie
 */

/**
 * Class F_Util_HttpFetch
 */
class F_Util_HttpFetch {
    /** Curl handler
     *
     */
    protected $curl;
    /** cookie字符串
     */
    protected $cookie;
    /** 源(用于最后结果调试)
     */
    protected $sourceStack = array();

    /** 得到源html栈
     */

    public function getSource() {
        return $this->sourceStack;
    }

    /**
     * get方式下载网页内容
     *
     * @param $url
     * @param int $timeout
     * @return mixed
     */
    public function get($url, $timeout = 1, $using_ms = false) {

        $this->curl = curl_init();

        curl_setopt($this->curl, CURLOPT_URL, $url);
        // 设置header
        curl_setopt($this->curl, CURLOPT_HEADER, 0);
        curl_setopt($this->curl, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)");
        curl_setopt($this->curl, CURLOPT_COOKIE, $this->cookie);
        if ($using_ms == false) {
            // 设置超时
            curl_setopt($this->curl, CURLOPT_TIMEOUT, $timeout);
        } else {
            // 设置超时
            curl_setopt($this->curl, CURLOPT_TIMEOUT_MS, $timeout);
        }

        // 设置cURL 参数，要求结果保存到字符串中还是输出到屏幕上。
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, 1);

        // 运行cURL，请求网页
        $data = curl_exec($this->curl);
        // 关闭URL请求
        curl_close($this->curl);
        // 找到cookie 放入cookiestring
        preg_match_all("/Set-Cookie:(.*?);/", $data, $match, PREG_SET_ORDER);
        foreach ($match as $r) {
            if ($this->cookie != '') {
                $this->cookie = $this->cookie . ';';
            }
            if (isset($r[1])) {
                $this->cookie .= trim(str_replace("\n", "", $r[1]));
            }
        }
        //放入调试栈
        array_push($this->sourceStack, " [$url] " . $data);

        return $data;
    }

    /**
     * POST方式下载网页内容
     *
     * @param $url
     * @param $params post的信息串
     * @return web conntent
     */

    public function post($url, $params, $timeout = 1, $using_ms = false) {

        $this->curl = curl_init();

        curl_setopt($this->curl, CURLOPT_URL, $url);

        // 设置header
        curl_setopt($this->curl, CURLOPT_HEADER, 0);
        curl_setopt($this->curl, CURLOPT_COOKIE, $this->cookie);
        curl_setopt($this->curl, CURLOPT_POST, 1);
        curl_setopt($this->curl, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)");
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, $params);
        if ($using_ms == false) {
            // 设置超时
            curl_setopt($this->curl, CURLOPT_TIMEOUT, $timeout);
        } else {
            // 设置超时
            curl_setopt($this->curl, CURLOPT_TIMEOUT_MS, $timeout);
        }

        // 设置cURL 参数，要求结果保存到字符串中还是输出到屏幕上。
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, 1);

        // 运行cURL，请求网页
        $data = curl_exec($this->curl);

        // 关闭URL请求
        curl_close($this->curl);
        // 找到cookie 放入cookiestring
        preg_match_all("/Set-Cookie:(.*?);/", $data, $match, PREG_SET_ORDER);

        foreach ($match as $r) {
            if ($this->cookie != '') {
                $this->cookie = $this->cookie . ';';
            }
            if (isset($r[1])) {
                $this->cookie .= trim(str_replace("\n", "", $r[1]));
            }
        }

        //放入调试栈
        array_push($this->sourceStack, " [$url] " . $data);

        return $data;
    }

}