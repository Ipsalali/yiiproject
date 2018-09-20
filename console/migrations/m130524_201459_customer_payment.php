<?php

use yii\db\Schema;
use yii\db\Migration;

class m130524_201459_customer_payment extends Migration
{       
    public $customer_payment = '{{%customer_payment}}';
    public $payment_state = '{{%payment_state}}';
    public $client = '{{%client}}';
    public $autotruck = '{{%autotruck}}';

    public function safeUp()
    {
        
        return true;
        /**
        * Table customer_payment
        */
        $sql = <<<SQL
            CREATE TABLE {$this->customer_payment}(
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `client_id` int(11) NOT NULL,
                `autotruck_id` int(11) NOT NULL,
                `payment_state_id` int(11) NOT NULL,
                `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                `comment` text NOT NULL,
                `sum` double NOT NULL DEFAULT '0',
                PRIMARY KEY (`id`),
                UNIQUE KEY `customer_payment_unique` (`client_id`,`autotruck_id`),

                CONSTRAINT `fk-customer_payment-client_id` FOREIGN KEY (`client_id`) REFERENCES {$this->client} (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
                CONSTRAINT `fk-customer_payment-autotruck_id` FOREIGN KEY (`autotruck_id`) REFERENCES {$this->autotruck} (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
                CONSTRAINT `fk-customer_payment-payment_state_id` FOREIGN KEY (`payment_state_id`) REFERENCES {$this->payment_state} (`id`) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB  DEFAULT CHARSET=utf8
SQL;

        $this->execute($sql);
    }

    public function safeDown()
    {
        return true
        $this->dropTable($this->customer_payment);
    }
}
