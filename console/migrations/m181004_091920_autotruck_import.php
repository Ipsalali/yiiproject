<?php

use console\migrations\Migration;

/**
 * Class m181004_091920_autotruck_import
 */
class m181004_091920_autotruck_import extends Migration
{   

    public $tableName = "{{%autotruck_import}}";
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $sql = <<<SQL
            CREATE TABLE {$this->tableName} (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `fileBinary` longblob,
              `name` varchar(50) DEFAULT NULL,
              `extension` varchar(50) NOT NULL,
              `creator` int(11) DEFAULT NULL,
              `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
              `isDeleted` smallint(6) DEFAULT '0',
              PRIMARY KEY (`id`),
              KEY `creator` (`creator`),
              CONSTRAINT `autotruck_import_ibfk_1` FOREIGN KEY (`creator`) REFERENCES `user` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8
SQL;

        $this->execute($sql);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {   
        $this->dropTable($this->tableName);
    }

   
}
