<?php
/**
 * Copyright 2013 princehaku
 *
 *  Author     : baizhongwei
 *  Blog       : http://3haku.net
 */

/** 错误和异常处理类
 *
 */
class F_Core_ErrorHandler {
    /*
     * 打印页面日志并结束脚本
     *
     */
    public static function dieErrorLogs() {
        // 打印日志
        if (DEV_MODE) {
            if (!headers_sent()) {
                header("Content-Type:text/html;encoding=utf-8;");
            }
            echo Flow::Log()->getHTML();
        }
        die;
    }

    public static function exceptionHandler($exception) {
        Flow::Log()->error($exception->getMessage());
        self::dieErrorLogs();
    }
    /**
     * 致命错误处理
     *
     */
    public static function fatalShutdownHandler() {
        if (null != ($error = error_get_last())) {
            switch ($error['type']) {
                case E_ERROR :
                case E_PARSE :
                case E_DEPRECATED:
                case E_CORE_ERROR :
                case E_COMPILE_ERROR :
                    Flow::Log()->error($error['message'] . " in " . $error['file'] . " line " . $error['line']);
                    self::dieErrorLogs();
            }
        }
    }

    /**
     * 负责分发错误到日志记录
     */
    public static function errorHandler($errno, $errstr, $errfile, $errline) {
        switch ($errno) {
            case E_NOTICE :
            case E_USER_NOTICE :
                $errors = "Notice";
                break;
            case E_WARNING :
            case E_USER_WARNING :
                $errors = "Warning";
                break;
            case E_ERROR :
            case E_USER_ERROR :
                $errors = "Fatal Error";
                break;
            default :
                $errors = "Unknown";
                break;
        }
        if ($errors == "Fatal Error") {
            Flow::Log()->error(sprintf("%s in %s on line %d", $errstr, $errfile, $errline));
        }
        if ($errors == "Warning") {
            Flow::Log()->debug(sprintf("%s in %s on line %d", $errstr, $errfile, $errline));
        } else {
            Flow::Log()->info(sprintf("%s in %s on line %d", $errstr, $errfile, $errline));
        }
        self::dieErrorLogs();

        return false;
    }

}
