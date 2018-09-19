<?php

use yii\db\Schema;
use yii\db\Migration;

class m130524_201462_expenses_manager extends Migration
{       
    public $expenses_manager = '{{%expenses_manager}}';
    public $organisation = '{{%organisation}}';
    public $user = '{{%user}}';
    public $autotruck = '{{%autotruck}}';

    public function safeUp()
    {
        

        /**
        * Table expenses_manager
        */
        $sql = <<<SQL
            CREATE TABLE {$this->expenses_manager}(
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `manager_id` int(11) NOT NULL,
                  `autotruck_id` int(11) NOT NULL,
                  `cost` double NOT NULL,
                  `comment` text NOT NULL,
                  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                  `organisation` int(11) NOT NULL DEFAULT '0',
                  PRIMARY KEY (`id`),

                CONSTRAINT `fk-expenses_manager-manager_id` FOREIGN KEY (`manager_id`) REFERENCES {$this->user} (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
                CONSTRAINT `fk-expenses_manager-autotruck_id` FOREIGN KEY (`autotruck_id`) REFERENCES {$this->autotruck} (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
                CONSTRAINT `fk-expenses_manager-organisation` FOREIGN KEY (`organisation`) REFERENCES {$this->organisation} (`id`) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB  DEFAULT CHARSET=utf8
SQL;

        $this->execute($sql);
    }

    public function safeDown()
    {
        $this->dropTable($this->expenses_manager);
    }
}
