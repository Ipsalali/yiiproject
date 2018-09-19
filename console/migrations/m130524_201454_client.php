<?php

use yii\db\Schema;
use yii\db\Migration;

class m130524_201454_client extends Migration
{       
    public $client = '{{%client}}';
    public $user = '{{%user}}';
    public $organisation = '{{%organisation}}';
    public $client_category = '{{%client_category}}';

    public function safeUp()
    {
        /**
        * Table client
        */
        $sql = <<<SQL
            CREATE TABLE {$this->client}(
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `name` varchar(255) NOT NULL,
              `full_name` varchar(255) CHARACTER SET ucs2 NOT NULL,
              `description` text NOT NULL,
              `phone` varchar(255) NOT NULL,
              `user_id` int(11) DEFAULT NULL,
              `client_category_id` int(11) DEFAULT NULL,
              `manager` int(11) DEFAULT NULL,
              `contract_number` varchar(255) NOT NULL,
              `payment_clearing` double NOT NULL,
              `organisation_pay_id` int(11) DEFAULT NULL,
              `email` text NOT NULL,
              PRIMARY KEY (`id`),

              CONSTRAINT `fk-client-organisation_pay_id` FOREIGN KEY (`organisation_pay_id`) REFERENCES {$this->organisation} (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
              CONSTRAINT `fk-client-user_id` FOREIGN KEY (`user_id`) REFERENCES {$this->user} (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
              CONSTRAINT `fk-client-manager` FOREIGN KEY (`manager`) REFERENCES {$this->user} (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
              CONSTRAINT `fk-client-client_category_id` FOREIGN KEY (`client_category_id`) REFERENCES {$this->client_category} (`cc_id`) ON DELETE SET NULL ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8
SQL;

        $this->execute($sql);
    }

    public function safeDown()
    {
        $this->dropTable($this->client);
    }
}
