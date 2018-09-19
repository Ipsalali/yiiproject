<?php

use yii\db\Schema;
use yii\db\Migration;

class m130524_201460_manager_country extends Migration
{       
    public $manager_country = '{{%manager_country}}';
    public $user = '{{%user}}';
    public $supplier_countries = '{{%supplier_countries}}';

    public function safeUp()
    {
        

        /**
        * Table manager_country
        */
        $sql = <<<SQL
            CREATE TABLE {$this->manager_country}(
                `user_id` int(11) NOT NULL,
                `country_id` int(11) NOT NULL,
                UNIQUE KEY `user_id` (`user_id`,`country_id`),

                CONSTRAINT `fk-manager_country-user_id` FOREIGN KEY (`user_id`) REFERENCES {$this->user} (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
                CONSTRAINT `fk-manager_country-country_id` FOREIGN KEY (`country_id`) REFERENCES {$this->supplier_countries} (`id`) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB  DEFAULT CHARSET=utf8
SQL;

        $this->execute($sql);
    }

    public function safeDown()
    {
        $this->dropTable($this->manager_country);
    }
}
