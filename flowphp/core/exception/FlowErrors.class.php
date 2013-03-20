<?php

/** 错误和异常处理类
 *
 */
class FlowErrors {

    /** 致命错误处理
     * 
     */
    static function fatalHandler() {
        if (null != ($error = error_get_last())) {
            switch ($error['type']) {
                case E_ERROR :
                case E_PARSE :
                case E_DEPRECATED:
                case E_CORE_ERROR :
                case E_COMPILE_ERROR :
                    L()->e($error['message'] . " in " . $error['file'] . " line " . $error['line']);
                    if(C("DEBUG")) {
                        L()->print_html();
                    }
            }
        }
    }

    /**
     * 负责分发错误到日志记录
     * Enter description here ...
     * @param unknown_type $errno
     * @param unknown_type $errstr
     * @param unknown_type $errfile
     * @param unknown_type $errline
     *
     * @author princehaku
     * @site http://3haku.net
     */
    static function errorHandler($errno, $errstr, $errfile, $errline) {
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
        //不显示系统的Notice错误
        if ($errors == "Notice" && strpos($errfile, "flowphp") > 0) {
            return false;
        }
        //
        if ($errors == "Fatal Error") {
            L()->e(sprintf("%s in %s on line %d", $errstr, $errfile, $errline));
        }
        if ($errors == "Warning") {
            L()->w(sprintf("%s in %s on line %d", $errstr, $errfile, $errline));
        } else {
            L()->i(sprintf("%s in %s on line %d", $errstr, $errfile, $errline));
        }

        return false;
    }

}
