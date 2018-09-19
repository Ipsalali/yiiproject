<?php

use yii\db\Schema;
use yii\db\Migration;

class m130524_201458_autotruck_notification extends Migration
{       
    public $autotruck_notification = '{{%autotruck_notification}}';
    public $autotruck = '{{%autotruck}}';
    public $app_status = '{{%app_status}}';
    public $app = '{{%app}}';
    public $client = '{{%client}}';

    public function safeUp()
    {
        /**
        * Table autotruck_notification
        */
        $sql = <<<SQL
            CREATE TABLE {$this->autotruck_notification}(
              `nid` int(11) NOT NULL AUTO_INCREMENT,
              `autotruck_id` int(11) NOT NULL,
              `status_id` int(11) NOT NULL,
              `client_id` int(11) NOT NULL,
              `app_id` int(11) NOT NULL,
              PRIMARY KEY (`nid`),
              UNIQUE KEY `autotruck_id` (`autotruck_id`,`status_id`,`client_id`,`app_id`),

              CONSTRAINT `fk-autotruck_notification-autotruck_id` FOREIGN KEY (`autotruck_id`) REFERENCES {$this->autotruck} (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
              CONSTRAINT `fk-autotruck_notification-status_id` FOREIGN KEY (`status_id`) REFERENCES {$this->app_status} (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
              CONSTRAINT `fk-autotruck_notification-client_id` FOREIGN KEY (`client_id`) REFERENCES {$this->client} (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
              CONSTRAINT `fk-autotruck_notification-app_id` FOREIGN KEY (`app_id`) REFERENCES {$this->app} (`id`) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8
SQL;

        $this->execute($sql);
    }

    public function safeDown()
    {
        $this->dropTable($this->autotruck_notification);
    }
}
