<?php

use yii\db\Schema;
use yii\db\Migration;

class m130524_201464_transfer extends Migration
{       
    public $transfer = '{{%transfer}}';
    public $transfers_package = '{{%transfers_package}}';
    public $client = '{{%client}}';

    public function safeUp()
    {
        
        /**
        * Table sellers
        */
        $sql = <<<SQL
            CREATE TABLE {$this->transfer} (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `package_id` int(11) NOT NULL,
              `client_id` int(11) NOT NULL,
              `name` varchar(255) NOT NULL,
              `sum` double NOT NULL,
              `sum_ru` double NOT NULL,
              `comment` text,
              `course` double DEFAULT NULL,
              `currency` int(3) DEFAULT NULL,
              PRIMARY KEY (`id`),

              CONSTRAINT `fk-transfer-package_id` FOREIGN KEY (`package_id`) REFERENCES {$this->transfers_package} (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
                CONSTRAINT `fk-transfer-client_id` FOREIGN KEY (`client_id`) REFERENCES {$this->client} (`id`) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8
SQL;

        $this->execute($sql);
    }



    public function safeDown()
    {
        $this->dropTable($this->transfer);
    }
}
