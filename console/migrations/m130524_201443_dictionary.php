<?php

use yii\db\Schema;
use yii\db\Migration;

class m130524_201443_dictionary extends Migration
{       
    public $type_packaging = '{{%type_packaging}}';
    public $supplier_countries = '{{%supplier_countries}}';
    public $payments = '{{%payments}}';
    public $client_category = '{{%client_category}}';

    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }


        /**
        * Table type_packaging
        */
        $this->createTable($this->type_packaging, [
            'id' => $this->primaryKey(),
            'title' => $this->string()->notNull()
        ], $tableOptions);


        /**
        * Table supplier_countries
        */
        $this->createTable($this->supplier_countries, [
            'id' => $this->primaryKey(),
            'country' => $this->string()->notNull(),
            'code' => $this->string()->notNull()
        ], $tableOptions);


        /**
        * Table payments
        */
        $sql = <<<SQL
            CREATE TABLE {$this->payments}(
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `title` varchar(255) NOT NULL,
              `code` int(11) NOT NULL,
              PRIMARY KEY (`id`),
              UNIQUE KEY `code` (`code`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8
SQL;

        $this->execute($sql);


        /**
        * Table client_category
        */
        $sql = <<<SQL
            CREATE TABLE {$this->client_category}(
              `cc_id` int(11) NOT NULL AUTO_INCREMENT,
              `cc_title` varchar(255) CHARACTER SET ucs2 NOT NULL,
              `cc_description` text CHARACTER SET ucs2 NOT NULL,
              `cc_parent` int(11) NOT NULL,
              PRIMARY KEY (`cc_id`)
            ) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8
SQL;

        $this->execute($sql);
        
    }

    public function safeDown()
    {   
        $this->dropTable($this->type_packaging);
        $this->dropTable($this->supplier_countries);
        $this->dropTable($this->payments);
        $this->dropTable($this->client_category);
    }
}
