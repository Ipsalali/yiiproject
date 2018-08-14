<?php

use yii\db\Migration;

class m180730_172058_payments_history extends Migration
{
    public $tableUser = "{{%user}}";
    public $tableOwn = "{{%payments_expenses}}";
    public $tableOwnJournal = "{{%payments_expenses_history}}";
    
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable($this->tableOwnJournal, [
            'id' => $this->primaryKey(),
            'entity_id' => $this->integer()->notNull(),
            
            'manager_id' => $this->integer()->null(),
            'sum' => $this->double()->null(),
            'date' => $this->timestamp()->null(),
            'comment' => $this->text()->null(),
            'organisation' => $this->integer()->null(),
            'payment' => $this->integer()->null(),
            'sum_cash' => $this->double()->null(),
            'sum_card' => $this->double()->null(),
            'sum_cash_us' => $this->double()->null(),
            'plus' => $this->smallInteger()->null()->defaultValue(0),
            'toreport' => $this->integer()->null()->defaultValue(1),
            'course' => $this->double()->null(),

            'created_at'=>$this->timestamp(),
            'type_action'=> $this->integer()->notNull(),
            'version'=> $this->integer()->notNull(),
            'creator_id'=> $this->integer()->null(),
            'isDeleted'=> $this->smallInteger()->null()->defaultValue(0),
        ], $tableOptions);


        $this->addForeignKey('fk-payments_expenses_history-entity_id', $this->tableOwnJournal,'entity_id', $this->tableOwn, 'id','CASCADE','CASCADE');
        $this->addForeignKey('fk-payments_expenses_history-creator_id', $this->tableOwnJournal,'creator_id', $this->tableUser, 'id','CASCADE','CASCADE');

        $this->addColumn($this->tableOwn,'version_id',$this->integer()->null());
        $this->addColumn($this->tableOwn,'isDeleted',$this->smallInteger()->null()->defaultValue(0));

        $this->addForeignKey('fk-payments_expenses-version_id', $this->tableOwn,'version_id', $this->tableOwnJournal, 'id','CASCADE','CASCADE');
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-payments_expenses_history-entity_id',$this->tableOwnJournal);
        $this->dropForeignKey('fk-payments_expenses_history-creator_id',$this->tableOwnJournal);
        
        $this->dropForeignKey('fk-payments_expenses-version_id',$this->tableOwn);
        
        $this->dropColumn($this->tableOwn,'version_id');
        $this->dropColumn($this->tableOwn,'isDeleted');

        $this->dropTable($this->tableOwnJournal);
    }
}
