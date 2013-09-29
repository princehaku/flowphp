<?php
/**
 * Copyright 2013 princehaku
 *
 *  FileName   : ArFactory.php
 *  Created on : 13-9-29 , ����11:53
 *  Author     : haku
 *  Blog       : http://3haku.net
 */
class F_DB_ArFactory extends F_DB_Basic {

    /**
     * ��ʼ�� aclҪ�����ָ������
     *
     * @return F_DB_ArManager
     */
    public function table($tablename, $new_instance = false) {
        if (empty($this->acm[$tablename]) || $new_instance) {
            $arm = new F_DB_ArManager();
            $arm->dbh = $this->dbh;
            $arm->tableinfo = $arm->setDBTable($this, $tablename);
            $this->acm[$tablename] = $arm;
        }
        return $this->acm[$tablename];
    }
}