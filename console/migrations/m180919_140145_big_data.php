<?php

use console\migrations\Migration;

/**
 * Class m180919_140145_import_app
 */
class m180919_140145_big_data extends Migration
{
    public $tables=[
        'app',
        'app_history',
        'autotruck_notification',
    ];

    public $file_frefix = 'webali_crmted_table_';

    public function safeUp()
    {   
        return true;
        if($this->tables && is_array($this->tables)){
            try {
                foreach ($this->tables as $table) {
                    $this->importFile($table,$this->file_frefix,true,true);
                }
            } catch (Exception $e) {
                echo "\n\n".$e->getMessage()."\n\n";
            }
            
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        return true;
        if($this->tables && is_array($this->tables)){
            $tables = array_reverse($this->tables);
            foreach ($tables as $table) {
                $this->delete($table);
            }
        }
    }
    
}
