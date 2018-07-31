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

        $this->dropColumn($this->transferPackageTable,'course');
        $this->dropColumn($this->transferPackageTable,'currency');
        $this->dropColumn($this->transferPackageHistoryTable,'course');
        $this->dropColumn($this->transferPackageHistoryTable,'currency');

        $this->addColumn($this->transferTable,'course',$this->double());
        $this->addColumn($this->transferTable,'currency',$this->integer(3));
        $this->addColumn($this->transferHistoryTable,'course',$this->double()->null());
        $this->addColumn($this->transferHistoryTable,'currency',$this->integer(3)->null());
    }

    public function safeDown()
    {
        $this->addColumn($this->transferPackageTable,'course',$this->double());
        $this->addColumn($this->transferPackageTable,'currency',$this->integer(3));
        $this->addColumn($this->transferPackageHistoryTable,'course',$this->double()->null());
        $this->addColumn($this->transferPackageHistoryTable,'currency',$this->integer(3)->null());

        $this->dropColumn($this->transferTable,'course');
        $this->dropColumn($this->transferTable,'currency');
        $this->dropColumn($this->transferHistoryTable,'course');
        $this->dropColumn($this->transferHistoryTable,'currency');
    }
    
}
