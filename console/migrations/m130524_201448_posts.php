<?php

use yii\db\Schema;
use yii\db\Migration;

class m130524_201448_posts extends Migration
{       
    public $post = '{{%post}}';

    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }


        /**
        * Table post
        */
        $sql = <<<SQL
            CREATE TABLE {$this->post}(
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `title` varchar(255) NOT NULL,
              `content` text NOT NULL,
              `created` datetime NOT NULL,
              `updated` datetime NOT NULL,
              `creator` int(11) NOT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8
SQL;

        $this->execute($sql);
    }

    public function safeDown()
    {
        $this->dropTable($this->post);
    }
}
