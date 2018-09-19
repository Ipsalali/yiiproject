<?php

use yii\db\Schema;
use yii\db\Migration;

class m130524_201461_payments_expenses extends Migration
{       
    public $payments_expenses = '{{%payments_expenses}}';
    public $organisation = '{{%organisation}}';
    public $user = '{{%user}}';

    public function safeUp()
    {
        

        /**
        * Table payments_expenses
        */
        $sql = <<<SQL
            CREATE TABLE {$this->payments_expenses}(
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `manager_id` int(11) NOT NULL,
                  `sum` double NOT NULL,
                  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                  `comment` text NOT NULL,
                  `organisation` int(11) DEFAULT NULL,
                  `payment` int(11) NOT NULL DEFAULT '0',
                  `sum_cash` double NOT NULL,
                  `sum_card` double NOT NULL,
                  `sum_cash_us` double NOT NULL,
                  `plus` tinyint(1) NOT NULL DEFAULT '0',
                  `toreport` int(11) NOT NULL DEFAULT '1',
                  `course` double DEFAULT NULL,
                  PRIMARY KEY (`id`),

                CONSTRAINT `fk-payments_expenses-manager_id` FOREIGN KEY (`manager_id`) REFERENCES {$this->user} (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
                CONSTRAINT `fk-payments_expenses-organisation` FOREIGN KEY (`organisation`) REFERENCES {$this->organisation} (`id`) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB  DEFAULT CHARSET=utf8
SQL;

        $this->execute($sql);
    }

    public function safeDown()
    {
        $this->dropTable($this->payments_expenses);
    }
}
