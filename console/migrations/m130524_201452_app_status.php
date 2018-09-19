<?php

use yii\db\Schema;
use yii\db\Migration;

class m130524_201452_app_status extends Migration
{       
    public $app_status = '{{%app_status}}';

    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }


        /**
        * Table app_status
        */
        $sql = <<<SQL
            CREATE TABLE {$this->app_status}(
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `title` varchar(255) NOT NULL,
              `description` text NOT NULL,
              `notification_template` text NOT NULL,
              `sort` int(11) NOT NULL,
              `send_check` tinyint(1) NOT NULL DEFAULT '0',
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8
SQL;

        $this->execute($sql);
    }

    public function safeDown()
    {
        $this->dropTable($this->app_status);
    }
}
