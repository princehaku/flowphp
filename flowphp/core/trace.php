<?php

/**
 * ͳ����
 * ������ϵͳlogger��simon
 *
 * @author        bzw <zhongwei.bzw@taobao.com>
 */
class F_Core_Trace {

    public $enable = true;

    protected $_stat_entries = array();

    /**
     * ��ǰͳ�Ƶ���״̬����
     * @return int
     */
    public function getStatNums() {
        return count($this->_stat_entries);
    }
    /**
     * ��ʼ��ʱ
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
     * ֹͣ��ʱ
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
     * ͳ��һ�ι���չ��
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
     * �㱨��log����
     */
    private function _reportLogger() {
        foreach ($this->_stat_entries as $stat_name => $stat_entry) {
            $log_row = "[{$stat_name}]��";
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
     * �㱨ͳ�ƵĽ��
     */
    public function report() {
        $this->_reportLogger();
        $this->clear();
    }
    /**
     * ������м�¼����Ϣ
     */
    public function clear() {
        $this->_stat_entries = array();
    }
}
