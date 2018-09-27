<?php

use yii\db\Schema;
use yii\db\Migration;

class m130524_201465_seller_expenses extends Migration
{       
    public $seller_expenses = '{{%seller_expenses}}';
    public $transfers_package = '{{%transfers_package}}';
    public $sellers = '{{%user}}';

    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }


        /**
        * Table sellers
        */
        $sql = <<<SQL
            CREATE TABLE {$this->seller_expenses} (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `package_id` int(11) NOT NULL,
                  `seller_id` int(11) NOT NULL,
                  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                  `sum` double NOT NULL,
                  `comment` text,
                  `sum_ru` double DEFAULT NULL,
                  `course` double DEFAULT NULL,
                  `currency` int(3) DEFAULT NULL,
                PRIMARY KEY (`id`),

                CONSTRAINT `fk-seller_expenses-package_id` FOREIGN KEY (`package_id`) REFERENCES {$this->transfers_package} (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
                CONSTRAINT `fk-seller_expenses-seller_id` FOREIGN KEY (`seller_id`) REFERENCES {$this->sellers} (`id`) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8
SQL;

        $this->execute($sql);
    }

    public function safeDown()
    {
        $this->dropTable($this->seller_expenses);
    }
}
