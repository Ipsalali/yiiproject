<?php

use yii\db\Schema;
use yii\db\Migration;

class m130524_201455_client_organisation extends Migration
{       
    public $client_organisation = '{{%client_organisation}}';
    public $client = '{{%client}}';
    public $organisation = '{{%organisation}}';

    public function safeUp()
    {
        /**
        * Table client_organisation
        */
        $sql = <<<SQL
            CREATE TABLE {$this->client_organisation}(
              `client_id` int(11) NOT NULL,
              `organisation_id` int(11) NOT NULL,
              `relation_number` varchar(255) NOT NULL,
              UNIQUE KEY `client_id` (`client_id`,`organisation_id`),

              CONSTRAINT `fk-client_organisation-client_id` FOREIGN KEY (`client_id`) REFERENCES {$this->client} (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
              CONSTRAINT `fk-client_organisation-organisation_id` FOREIGN KEY (`organisation_id`) REFERENCES {$this->organisation} (`id`) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8
SQL;

        $this->execute($sql);
    }

    public function safeDown()
    {
        $this->dropTable($this->client_organisation);
    }
}
