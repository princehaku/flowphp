<?php

/** 错误和异常处理类
 *
 */
class F_Core_Errors {

    public static function exceptionHandler() {

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
            }
        }
    }

    /**
     * 负责分发错误到日志记录
     */
    public static function errorHandler($errno, $errstr, $errfile, $errline) {
        // disable error capturing to avoid recursive errors
        restore_error_handler();
        restore_exception_handler();
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
        // 不显示系统的错误
        if (strpos($errfile, "flowphp") > 0) {
            Flow::Log()->error(sprintf("%s in %s on line %d", $errstr, $errfile, $errline));
            return false;
        }
        if ($errors == "Fatal Error") {
            Flow::Log()->error(sprintf("%s in %s on line %d", $errstr, $errfile, $errline));
        }
        if ($errors == "Warning") {
            Flow::Log()->debug(sprintf("%s in %s on line %d", $errstr, $errfile, $errline));
        } else {
            Flow::Log()->info(sprintf("%s in %s on line %d", $errstr, $errfile, $errline));
        }

        return false;
    }

}
