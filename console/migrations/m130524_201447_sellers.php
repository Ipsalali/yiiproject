<?php

use yii\db\Schema;
use yii\db\Migration;

class m130524_201447_sellers extends Migration
{       
    public $sellers = '{{%sellers}}';

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
            CREATE TABLE {$this->sellers} (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `name` varchar(255) NOT NULL,
              `full_name` varchar(255) NOT NULL,
              `description` text,
              `phone` varchar(255) DEFAULT NULL,
              `email` varchar(255) NOT NULL,
              PRIMARY KEY (`id`),
              UNIQUE KEY `phone` (`phone`,`email`)
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8
SQL;

        $this->execute($sql);
    }

    public function safeDown()
    {
        $this->dropTable($this->sellers);
    }
}
