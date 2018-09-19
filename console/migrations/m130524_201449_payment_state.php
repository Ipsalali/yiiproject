<?php

use yii\db\Schema;
use yii\db\Migration;

class m130524_201449_payment_state extends Migration
{       
    public $payment_state = '{{%payment_state}}';

    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }


        /**
        * Table payment_state
        */
        $sql = <<<SQL
            CREATE TABLE {$this->payment_state}(
               `id` int(11) NOT NULL AUTO_INCREMENT,
                  `title` varchar(255) NOT NULL,
                  `color` varchar(30) NOT NULL,
                  `default_value` tinyint(1) NOT NULL DEFAULT '0',
                  `filter` tinyint(1) NOT NULL DEFAULT '1',
                  `end_state` tinyint(1) NOT NULL DEFAULT '0',
                  `sum_state` tinyint(1) DEFAULT '0',
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB  DEFAULT CHARSET=utf8
SQL;

        $this->execute($sql);
    }

    public function safeDown()
    {
        $this->dropTable($this->payment_state);
    }
}
