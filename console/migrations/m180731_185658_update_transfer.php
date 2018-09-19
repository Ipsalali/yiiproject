<?php

use yii\db\Migration;

class m180731_185658_update_transfer extends Migration
{
    
    public $transferTable = "{{%transfer}}";
    public $transferPackageTable = "{{%transfers_package}}";


    public $transferHistoryTable = "{{%transfer_history}}";
    public $transferPackageHistoryTable = "{{%transfers_package_history}}";

    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
        return true;
    }

    public function safeDown()
    {
        return true;
    }
    
}
