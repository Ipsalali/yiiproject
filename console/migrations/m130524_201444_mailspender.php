<?php

use yii\db\Schema;
use yii\db\Migration;

class m130524_201444_mailspender extends Migration
{       
    public $spender = '{{%spender}}';

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
        $sql = <<<SQL
            CREATE TABLE {$this->spender} (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `from_staff` int(11) NOT NULL,
              `from_email` text,
              `date` datetime DEFAULT NULL,
              `to_client` text NOT NULL,
              `to_email` text,
              `theme` varchar(255) NOT NULL,
              `body` text NOT NULL,
              `sended` tinyint(1) NOT NULL DEFAULT '0',
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8
SQL;

        $this->execute($sql);
    }

    public function safeDown()
    {
        $this->dropTable($this->spender);
    }
}
