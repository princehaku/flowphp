<?php

/**
 * 统计类
 * 集成了系统logger
 *
 * @author        bzw <zhongwei.bzw@taobao.com>
 */
class F_Core_Trace {

    public $enable = true;

    protected $_stat_entries = array();

    /**
     * 当前统计到的状态条数
     * @return int
     */
    public function getStatNums() {
        return count($this->_stat_entries);
    }
    /**
     * 开始计时
     *
     * @param $stat_name
     */
    public function start($stat_name) {
        if ($this->enable == false) {
            return;
        }
        $this->_stat_entries[$stat_name]['start_time_ms'] = microtime(1) * 1000;

    }

    /**
     * 停止计时
     *
     * @param $stat_name
     */
    public function stop($stat_name) {
        if ($this->enable == false) {
            return;
        }
        if (empty($this->_stat_entries[$stat_name]['start_time_ms'])) {
            throw new FlowException('No Begin,No Ends');
        }
        $this->_stat_entries[$stat_name]['end_time_ms'] = microtime(1) * 1000;
    }
    /**
     * 统计一次功能展现
     *
     * @param $stat_name
     * @param $is_hits
     */
    public function setShowUp($stat_name) {
        if ($this->enable == false) {
            return;
        }
        $this->_stat_entries[$stat_name]['showup'] = true;
    }
    /**
     * 汇报到log里面
     */
    private function _reportLogger() {
        foreach ($this->_stat_entries as $stat_name => $stat_entry) {
            $log_row = "[{$stat_name}]：";
            if (isset($stat_entry['end_time_ms'])) {
                $stat_entry['cost'] = $stat_entry['end_time_ms'] - $stat_entry['start_time_ms'];
                unset($stat_entry['end_time_ms']);
                unset($stat_entry['start_time_ms']);
            }
            foreach ($stat_entry as $entry_key => $entry_val) {
                $log_row .= "{$entry_key} {$entry_val} ";
            }
            Flow::Log()->debug($log_row);
        }
    }
    /**
     * 汇报统计的结果
     */
    public function report() {
        $this->_reportLogger();
        $this->clear();
    }
    /**
     * 清除所有记录的信息
     */
    public function clear() {
        $this->_stat_entries = array();
    }
}
