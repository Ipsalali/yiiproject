<?php

use yii\db\Migration;

class m180731_191213_update_seller_expenses extends Migration
{
    public $sellerExpenseTable = "{{%seller_expenses}}";
    public $sellerExpenseTableHistory = "{{%seller_expenses_history}}";



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
