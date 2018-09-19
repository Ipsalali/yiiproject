<?php

use yii\db\Schema;
use yii\db\Migration;

class m130524_201456_app_trace extends Migration
{       
    public $app_trace = '{{%app_trace}}';
    public $autotruck = '{{%autotruck}}';
    public $app_status = '{{%app_status}}';

    public function safeUp()
    {
        /**
        * Table client UNIQUE KEY `app_trace_unique_autotruck_id` (`autotruck_id`,`status_id`),
        */
        $sql = <<<SQL
            CREATE TABLE {$this->app_trace}(
              `trace_id` int(11) NOT NULL AUTO_INCREMENT,
              `autotruck_id` int(11) NOT NULL,
              `status_id` int(11) NOT NULL,
              `traсe_first` tinyint(1) NOT NULL,
              `traсe_last` tinyint(1) NOT NULL,
              `prevstatus_id` int(11) NOT NULL,
              `trace_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
              PRIMARY KEY (`trace_id`),
              
              CONSTRAINT `fk-app_trace-status_id` FOREIGN KEY (`status_id`) REFERENCES {$this->app_status} (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
              CONSTRAINT `fk-app_trace-prevstatus_id` FOREIGN KEY (`prevstatus_id`) REFERENCES {$this->app_status} (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
              CONSTRAINT `fk-app_trace-autotruck_id` FOREIGN KEY (`autotruck_id`) REFERENCES {$this->autotruck} (`id`) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8
SQL;

        $this->execute($sql);
    }

    public function safeDown()
    {
        $this->dropTable($this->app_trace);
    }
}
