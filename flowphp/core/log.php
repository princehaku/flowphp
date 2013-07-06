<?php
/**
 * Copyright 2013 princehaku
 *
 *  Author     : baizhongwei
 *  Blog       : http://3haku.net
 */

/**
 * 日志类 记录用户的数据库和其他历史信息
 * 用于调试
 */
class F_Core_Log {

    const LEVEL_DEBUG = 1;

    const LEVEL_INFO = 2;

    const LEVEL_ERROR = 4;
    /** 记录的数量
     *
     * @access private
     */
    private $recordNums = 0;

    /** 记录表的数组
     *
     */
    private $msg = array();

    public $logLevel = 7;
    /**
     * trace log的来源
     *
     * @return $this
     */
    public function traceLog($debugtrace = null) {
        if (!(isset(Flow::$cfg["trace_error"]) && Flow::$cfg["trace_error"] == false)) {
            if (empty($debugtrace)) {
                $debugtrace = debug_backtrace();
            }
            for ($i = 0; $i < count($debugtrace); $i++) {
                $j = $debugtrace[$i];
                if (empty($j['file'])) {
                    continue;
                }
                $flow_path = dirname(FLOW_PATH) . "/" . basename(FLOW_PATH);
                // sys内的不trace
                if (strpos(str_replace("\\", "/", $j['file']),
                    str_replace("\\", "/", $flow_path)) !== false
                ) {
                    //continue;
                }
                $this->msg[$this->recordNums++] = array(
                    'type' => "trace",
                    'msg' => "Trace :" . $j['file'] . "  " . $j['line'] . "行"
                );
            }
        }
        return $this;
    }

    /**
     * 记录调试信息
     *
     * @param $msg
     * @return F_Core_Log
     */
    public function debug($msg, $cat = "") {
        if ($this->logLevel & self::LEVEL_DEBUG != self::LEVEL_DEBUG) {
            return;
        }
        $this->msg[$this->recordNums++] = array(
            'type' => 'debug',
            'cat' => $cat,
            'msg' => $msg
        );

        return $this;
    }
    /**
     * 记录信息
     *
     * @param $msg
     * @return $this
     */
    public function info($msg, $cat = "") {
        if ($this->logLevel & self::LEVEL_INFO != self::LEVEL_INFO) {
            return;
        }
        $this->msg[$this->recordNums++] = array(
            'type' => 'info',
            'cat' => $cat,
            'msg' => $msg
        );
        return $this;
    }

    /**
     * 记录错误
     *
     * @param $msg
     * @return $this
     */
    public function error($msg, $cat = "") {
        if ($this->logLevel & self::LEVEL_ERROR != self::LEVEL_ERROR) {
            return;
        }
        $this->msg[$this->recordNums++] = array(
            'type' => 'error',
            'cat' => $cat,
            'msg' => $msg
        );
        $this->traceLog();
        return $this;
    }

    /** 清空当前所有日志
     * 并将记录数置0
     */
    public function clear() {
        $this->recordNums = 0;
        $this->msg = null;
    }

    /**
     * 得到html的
     */
    public function getHTML() {
        $warp = "";
        if (count($this->msg) > 10) {
            $warp = "height:300px;overflow-y:scroll";
        }
        $c = "<div class='syslog' style='border:1px solid;padding:20px;'>运行日志:<br/><div style='border:1px dashed;$warp'><div>";
        foreach (($this->msg) as $i => $j) {
            $c .= $j['msg'] . "<br />";
        }
        $c .= "</div></div></div>";
        return $c;
    }

    /**
     * 得到JSON格式
     *
     */
    public function getJSON() {
        return json_encode($this->msg);
    }

    /**
     * 得到xml
     */
    public function getXML() {
        $content = "<?xml version=\"1.0\" encoding=\"utf-8\" ?>\n";
        $content .= "<logs>";
        foreach (($this->msg) as $i => $j) {
            $content .= "<log><type>" . ($j->getType()) . "</type>\n";
            $content .= "<message><![CDATA[" . ($j->getMessage()) . "]]></message></log>\n";
        }
        $content .= "</logs>";
        return $content;
    }

    /**
     * 得到数据
     */
    public function getDatas() {
        return $this->msg;
    }
}
