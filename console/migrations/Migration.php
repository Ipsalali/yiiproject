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



    public function importFile($fileName,$file_prefix = '',$checkFK = true){
        echo "\n\n Start import {$fileName};\n\n ";

        $file = $file_prefix . $fileName.".sql";
        $path = __DIR__."/../import/";
        if(file_exists($path.$file)){
            $sql = file_get_contents($path.$file);
            
            if($sql === false ){
                echo "{$file} is empty;";
                return false;
            }

            $sql = $checkFK ? "SET FOREIGN_KEY_CHECKS=0;".$sql : $sql;
            $this->execute($sql);
        }else{
            echo "{$file} not found;";
        }
        
        echo "\n\n finish import {$file};\n\n ";
    }
}

?>