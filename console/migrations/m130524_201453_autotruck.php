<?php

use yii\db\Schema;
use yii\db\Migration;

class m130524_201453_autotruck extends Migration
{       
    public $autotruck = '{{%autotruck}}';
    public $user = '{{%user}}';
    public $app_status = '{{%app_status}}';

    public function safeUp()
    {
        /**
        * Table autotruck
        */
        $sql = <<<SQL
            CREATE TABLE {$this->autotruck}(
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `name` varchar(255) NOT NULL,
              `invoice` varchar(255) NULL,
              `number` int(11) NOT NULL,
              `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
              `description` text NOT NULL,
              `status` int(11) NOT NULL,
              `course` double NOT NULL DEFAULT '0',
              `country` int(11) NOT NULL,
              `file` text,
              `auto_number` varchar(255) NOT NULL,
              `auto_name` varchar(255) NOT NULL,
              `gtd` varchar(255) NOT NULL,
              `decor` varchar(255) NOT NULL DEFAULT '',
              `creator` int(11) NULL,
              PRIMARY KEY (`id`),

              CONSTRAINT `fk-autotruck-status` FOREIGN KEY (`status`) REFERENCES {$this->app_status} (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
              CONSTRAINT `fk-autotruck-creator` FOREIGN KEY (`creator`) REFERENCES {$this->user} (`id`) ON DELETE SET NULL ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8
SQL;

        $this->execute($sql);
    }

    public function safeDown()
    {
        $this->dropTable($this->autotruck);
    }
}
