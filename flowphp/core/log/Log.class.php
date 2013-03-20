<?php

/** 日志消息类
 * 
 * @author princehaku
 *
 */
class message {

    protected $type;
    protected $message;

    public function __construct($type, $message) {
        $this->type = $type;
        $this->message = $message;
    }

    public function getColorMessage() {
        if ($this->type == "INFO") {
            $color = "";
        }
        if ($this->type == "WARN") {
            $color = "orange";
        }
        if ($this->type == "ERROR") {
            $color = "red";
        }
        return "<font color=$color>" . $this->type . ":" . htmlspecialchars_deep($this->message) . "</font>";
    }

    public function getMessage() {
        return $this->message;
    }

    public function getType() {
        return $this->type;
    }

}

/** 日志类 记录用户的数据库和其他历史信息
 * 用于调试
 * @author princehaku
 * @site http://3haku.net
 */
class Log {

    /** 记录的数量
     *
     * @access private
     */
    private $recordNums = 0;

    /** 记录表的数组
     *
     */
    private $msg = array();

    /** 记录信息
     *
     * @param $msg
     */
    public function i($msg) {
        if (C("LOG_PRI") >= 3) {
            $this->msg[$this->recordNums++] = new message("INFO", $msg);
        }
        return $this;
    }

    /** 记录警告
     *
     * @param $msg
     */
    public function w($msg) {
        if (C("LOG_PRI") >= 2) {
            $this->msg[$this->recordNums++] = new message("WARN", $msg);
            $debugtrace = debug_backtrace();
            for ($i = 0; $i < count($debugtrace); $i++) {
                $j = $debugtrace[$i];
                if (array_key_exists("file", $j) && !strstr($j['file'], "flowphp")) {
                    $this->msg[$this->recordNums++] = new message("WARN", "Trace :" . $j['file'] . "  " . $j['line'] . "行");
                }
            }
        }
        return $this;
    }

    /** 记录错误
     *
     * @param $msg
     */
    public function e($msg) {
        if (C("LOG_PRI") >= 1) {
            $this->msg[$this->recordNums++] = new message("ERROR", $msg);
            $debugtrace = debug_backtrace();
            foreach ($debugtrace as $j) {
                if (array_key_exists("file", $j) && !strstr($j['file'], "flowphp")) {
                    $this->msg[$this->recordNums++] = new message("ERROR", "Trace :" . $j['file'] . "  " . $j['line'] . "行");
                }
            }
        }
        return $this;
    }

    /** 清空当前所有日志
     * 并将记录数置0
     */
    public function clear() {
        $this->recordNums = 0;
        $this->msg = null;
    }

    /** 打印致命错误并终止程序
     *
     * @param $msg
     */
    public function f($msg) {
        $this->e($msg);
        $this->print();
        die;
    }

    /** 输出日志
     *
     */
    public function print_html() {
        $warp = "";
        if (count($this->msg) > 10) {
            $warp = "height:300px;overflow-y:scroll";
        }
        echo "<div class='syslog' style='background-color:#eee;border:1px solid;padding:20px;'>运行日志:<br/><div style='border:1px dashed;$warp'><div>";
        foreach (($this->msg) as $i => $j) {
            echo ($j->getColorMessage()) . "<br />";
        }
        echo "</div></div></div>";
    }

    /** 输出日志(JSON格式)
     *
     */
    public function print_json() {
        echo json_encode($this->msg);
    }
    /** 将日志存入文件
     * @param $filename
     */
    public function save($filename) {
        $content = "";
        foreach (($this->msg) as $i => $j) {
            $content .= ($j->getMessage()) . "\n";
        }
        file_put_contents($filename, $content);
    }

    /** 得到xml
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

}
