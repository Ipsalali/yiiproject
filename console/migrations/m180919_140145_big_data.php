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

    public $file_frefix = 'webali_tcrm_table_';

    public function safeUp()
    {   
        if($this->tables && is_array($this->tables)){
            foreach ($this->tables as $table) {
                $this->importFile($table,$this->file_frefix);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        if($this->tables && is_array($this->tables)){
            $tables = array_reverse($this->tables);
            foreach ($tables as $table) {
                $this->delete($table);
            }
        }
    }
    
}
