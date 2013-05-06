<?php
/**
 * Copyright 2013 princehaku
 *
 *  Author     : baizhongwei
 *  Blog       : http://3haku.net
 */

/**
 * Request 类 方便的从参数获取数据
 *
 * @author princehaku
 * @site http://3haku.net
 */
class F_Cli_Request {

    protected $params;

    function __construct() {
        $argv = $_SERVER['argv'];
        foreach ($argv as $arg) {
            // 先按照=拆分
            $p = explode("=", $arg);
            if (count($p) != 2) {
                continue;
            }
            $key = ltrim($p[0], "-");
            $val = $p[1];
            // 支持"符号
            if (isset($val[0]) && $val[0] == "\"" && strrpos($val, "\"", count($val) -1) === count($val)) {
                $val = substr($val, 1, -1);
                $val = str_replace("\\\"", "\"", $val);
            }
            $this->params[$key] = $val;
        }
    }
    /**
     * 从命令行的输入中 取得一个参数值
     * 可以从形如a=b -a=b --a=b中取得
     *
     * @param $key
     * @param null $default
     * @return null
     */
    public function opt($key, $default = null) {
        if (isset($this->params[$key])) {
            return $this->params[$key];
        } else {
            return $default;
        }
    }
    /**
     * 从标准输入流中读取一段
     * @param null $promote
     * @return string
     */
    public function input($promote = null) {
        if ($promote) {
            echo $promote . PHP_EOL;
        }
        $stdin = fopen("php://stdin", "r");
        return fgets($stdin);
    }
}
