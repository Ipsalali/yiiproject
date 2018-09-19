<?php

use yii\db\Schema;
use yii\db\Migration;

class m130524_201463_transfers_package extends Migration
{       
    public $transfers_package = '{{%transfers_package}}';

    public function safeUp()
    {
        
        /**
        * Table sellers
        */
        $sql = <<<SQL
            CREATE TABLE {$this->transfers_package} (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `name` varchar(255) NOT NULL,
              `date` timestamp NULL DEFAULT NULL,
              `comment` text NOT NULL,
              `status_date` timestamp NULL DEFAULT NULL,
              `status` int(3) NOT NULL,
              `files` text,
              `version_id` int(11) DEFAULT NULL,
              `isDeleted` tinyint(4) NOT NULL DEFAULT '0',
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8
SQL;

        $this->execute($sql);


        /**
        * Table sellers
        */
        $sql = <<<SQL
            CREATE TABLE `transfers_package_history` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `entity_id` int(11) NOT NULL,
              `name` varchar(255) NOT NULL,
              `date` timestamp NULL DEFAULT NULL,
              `comment` text NOT NULL,
              `status_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
              `status` int(3) NOT NULL,
              `files` text,
              `version` int(11) NOT NULL,
              `creator_id` int(11) NOT NULL,
              `type_action` int(11) NOT NULL,
              `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
              `isDeleted` tinyint(4) DEFAULT '0',
              PRIMARY KEY (`id`),
              CONSTRAINT `fk-transfers_package_history-creator_id` FOREIGN KEY (`creator_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
              CONSTRAINT `fk-transfers_package_history-entity_id` FOREIGN KEY (`entity_id`) REFERENCES {$this->transfers_package} (`id`) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8
SQL;

        $this->execute($sql);


        $this->addForeignKey('fk-transfers_package-version_id', $this->transfers_package,'version_id', 'transfers_package_history', 'id','CASCADE','CASCADE');

    }



    public function safeDown()
    { 
      $this->dropForeignKey('fk-transfers_package-version_id',$this->transfers_package);
      $this->dropTable('transfers_package_history');
      $this->dropTable($this->transfers_package);
    }
}
