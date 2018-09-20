<?php

use yii\db\Schema;
use yii\db\Migration;

class m130524_201457_app extends Migration
{       
    public $app = '{{%app}}';
    public $autotruck = '{{%autotruck}}';
    public $client = '{{%client}}';
    public $sender = '{{%sender}}';
    public $type_packaging = '{{%type_packaging}}';

    public function safeUp()
    {
        /**
        * Table client
        */
        $sql = <<<SQL
            CREATE TABLE {$this->app}(
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `client` int(11) DEFAULT NULL,
              `weight` double NOT NULL,
              `rate` double NOT NULL,
              `summa_us` double NOT NULL,
              `status` int(11) NOT NULL,
              `comment` text NOT NULL,
              `info` text NOT NULL,
              `autotruck_id` int(11) NOT NULL,
              `type` enum('0','1') NOT NULL DEFAULT '0',
              `out_stock` tinyint(1) NOT NULL DEFAULT '0',
              `sender` int(11) DEFAULT NULL,
              `count_place` int(11) DEFAULT NULL,
              `package` int(11) DEFAULT NULL,
              PRIMARY KEY (`id`),

              CONSTRAINT `fk-app-client` FOREIGN KEY (`client`) REFERENCES {$this->client} (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
              CONSTRAINT `fk-app-autotruck_id` FOREIGN KEY (`autotruck_id`) REFERENCES {$this->autotruck} (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
              CONSTRAINT `fk-app-sender` FOREIGN KEY (`sender`) REFERENCES {$this->sender} (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
              CONSTRAINT `fk-app-package` FOREIGN KEY (`package`) REFERENCES {$this->type_packaging} (`id`) ON DELETE SET NULL ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8
SQL;

        $this->execute($sql);
    }

    public function safeDown()
    {
        $this->dropTable($this->app);
    }
}
