<?php

use yii\db\Migration;

class m180730_171955_transfer_history extends Migration
{
    public $tableUser = "{{%user}}";
    public $tableOwn = "{{%transfer}}";
    public $tableOwnJournal = "{{%transfer_history}}";
    
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
            
            'package_id' => $this->integer()->null(),
            'client_id' => $this->integer()->null(),
            'name' => $this->string()->null(),
            'sum' => $this->double()->null(),
            'sum_ru' => $this->double()->null(),
            'comment' => $this->text()->null(),
            'course'=>$this->double()->null(),
            'currency'=>$this->integer(3)->null(),

            'created_at'=>$this->timestamp(),
            'type_action'=> $this->integer()->notNull(),
            'version'=> $this->integer()->notNull(),
            'creator_id'=> $this->integer()->null(),
            'isDeleted'=> $this->smallInteger()->null()->defaultValue(0),
        ], $tableOptions);


        $this->addForeignKey('fk-transfer_history-entity_id', $this->tableOwnJournal,'entity_id', $this->tableOwn, 'id','CASCADE','CASCADE');
        $this->addForeignKey('fk-transfer_history-creator_id', $this->tableOwnJournal,'creator_id', $this->tableUser, 'id','CASCADE','CASCADE');

        $this->addColumn($this->tableOwn,'version_id',$this->integer()->null());
        $this->addColumn($this->tableOwn,'isDeleted',$this->smallInteger()->null()->defaultValue(0));

        $this->addForeignKey('fk-transfer-version_id', $this->tableOwn,'version_id', $this->tableOwnJournal, 'id','CASCADE','CASCADE');
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-transfer_history-entity_id',$this->tableOwnJournal);
        $this->dropForeignKey('fk-transfer_history-creator_id',$this->tableOwnJournal);
        
        $this->dropForeignKey('fk-transfer-version_id',$this->tableOwn);
        
        $this->dropColumn($this->tableOwn,'version_id');
        $this->dropColumn($this->tableOwn,'isDeleted');

        $this->dropTable($this->tableOwnJournal);
    }
}
