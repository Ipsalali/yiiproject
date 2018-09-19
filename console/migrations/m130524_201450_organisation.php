<?php

use yii\db\Schema;
use yii\db\Migration;

class m130524_201450_organisation extends Migration
{       
    public $organisation = '{{%organisation}}';

    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }


        /**
        * Table organisation
        */
        $sql = <<<SQL
            CREATE TABLE {$this->organisation}(
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `bank_name` varchar(255) CHARACTER SET utf16 NOT NULL,
              `bik` varchar(255) NOT NULL,
              `bank_check` varchar(255) NOT NULL,
              `inn` varchar(255) NOT NULL,
              `kpp` varchar(255) NOT NULL,
              `org_name` varchar(255) CHARACTER SET utf16 NOT NULL,
              `org_check` varchar(255) NOT NULL,
              `org_address` varchar(255) NOT NULL,
              `active` tinyint(1) NOT NULL,
              `is_stoped` tinyint(1) NOT NULL DEFAULT '0',
              `headman` varchar(255) NOT NULL,
              `payment` int(11) NOT NULL DEFAULT '0',
              `description` text NOT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB  DEFAULT CHARSET=utf8
SQL;

        $this->execute($sql);
    }

    public function safeDown()
    {
        $this->dropTable($this->organisation);
    }
}
