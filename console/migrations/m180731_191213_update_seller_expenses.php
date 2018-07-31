<?php

use yii\db\Migration;

class m180731_191213_update_seller_expenses extends Migration
{
    public $sellerExpenseTable = "{{%seller_expenses}}";
    public $sellerExpenseTableHistory = "{{%seller_expenses_history}}";



    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
        $this->addColumn($this->sellerExpenseTable,'sum_ru',$this->double()->null());
        $this->addColumn($this->sellerExpenseTableHistory,'sum_ru',$this->double()->null());


        $this->addColumn($this->sellerExpenseTable,'course',$this->double());
        $this->addColumn($this->sellerExpenseTableHistory,'course',$this->double());
        
        $this->addColumn($this->sellerExpenseTable,'currency',$this->integer(3));
        $this->addColumn($this->sellerExpenseTableHistory,'currency',$this->integer(3));
    }

    public function safeDown()
    {
        
        $this->dropColumn($this->sellerExpenseTable,'sum_ru');
        $this->dropColumn($this->sellerExpenseTableHistory,'sum_ru');

        $this->dropColumn($this->sellerExpenseTable,'course');
        $this->dropColumn($this->sellerExpenseTableHistory,'course');

        $this->dropColumn($this->sellerExpenseTable,'currency');
        $this->dropColumn($this->sellerExpenseTableHistory,'currency');
    }
}
