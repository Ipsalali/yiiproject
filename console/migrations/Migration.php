<?php

namespace console\migrations;

use yii\db\Migration as yiiMigration;

class Migration extends yiiMigration{




	protected function isOracle()
    {
        return $this->db->driverName === 'oci';
    }


	/**
     * @return bool
     */
    protected function isMSSQL()
    {
        return $this->db->driverName === 'mssql' || $this->db->driverName === 'sqlsrv' || $this->db->driverName === 'dblib';
    }


    protected function buildFkClause($delete = '', $update = '')
    {
        if ($this->isMSSQL()) {
            return '';
        }

        if ($this->isOracle()) {
            return ' ' . $delete;
        }

        return implode(' ', ['', $delete, $update]);
    }
}

?>