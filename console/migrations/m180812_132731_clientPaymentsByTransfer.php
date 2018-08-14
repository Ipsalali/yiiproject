<?php

use yii\db\Migration;

class m180812_132731_clientPaymentsByTransfer extends Migration
{
    
    public $tableUser = "{{%user}}";
    public $tableOwn = "{{%payment_client_by_transfer}}";
    public $tableOwnJournal = "{{%payment_client_by_transfer_history}}";
    
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable($this->tableOwn, [
            'id' => $this->primaryKey(),

            'date'=>$this->timestamp()->null(),
            'client_id' => $this->integer()->notNull(),
            'currency' => $this->integer(3)->null()->defaultValue(0),
            'course' => $this->double()->null()->defaultValue(0),
            'sum' => $this->double()->null()->defaultValue(0),
            'sum_ru' => $this->double()->null()->defaultValue(0),
            'contractor_org'=> $this->integer()->null(),
            'contractor_seller'=> $this->integer()->null(),
            'comment'=>$this->string()->null(),

            'version_id'=> $this->integer()->null(),
            'isDeleted'=> $this->smallInteger()->null()->defaultValue(0),
        ], $tableOptions);

        $this->addForeignKey('fk-payment_client_by_transfer-contractor_org',$this->tableOwn,'contractor_org',"{{%organisation}}",'id','CASCADE','CASCADE');
        $this->addForeignKey('fk-payment_client_by_transfer-client_id',$this->tableOwn,'client_id',"{{%client}}",'id','CASCADE','CASCADE');
        


        $this->createTable($this->tableOwnJournal, [
            'id' => $this->primaryKey(),
            'entity_id' => $this->integer()->notNull(),
            
            'date'=>$this->timestamp()->null(),
            'client_id' => $this->integer()->notNull(),
            'currency' => $this->integer(3)->null()->defaultValue(0),
            'course' => $this->double()->null()->defaultValue(0),
            'sum' => $this->double()->null()->defaultValue(0),
            'sum_ru' => $this->double()->null()->defaultValue(0),
            'contractor_org'=> $this->integer()->null(),
            'contractor_seller'=> $this->integer()->null(),
            'comment'=>$this->string()->null(),

            'created_at'=>$this->timestamp(),
            'type_action'=> $this->integer()->notNull(),
            'version'=> $this->integer()->notNull(),
            'creator_id'=> $this->integer()->null(),
            'isDeleted'=> $this->smallInteger()->null()->defaultValue(0),
        ], $tableOptions);


        $this->addForeignKey('fk-payment_client_by_transfer_history-entity_id', $this->tableOwnJournal,'entity_id', $this->tableOwn, 'id','CASCADE','CASCADE');
        $this->addForeignKey('fk-payment_client_by_transfer_history-creator_id', $this->tableOwnJournal,'creator_id', $this->tableUser, 'id','CASCADE','CASCADE');

        $this->addForeignKey('fk-payment_client_by_transfer_history-contractor_org',$this->tableOwnJournal,'contractor_org',"{{%organisation}}", 'id','CASCADE','CASCADE');
        $this->addForeignKey('fk-payment_client_by_transfer_history-client_id',$this->tableOwnJournal,'client_id',"{{%client}}", 'id','CASCADE','CASCADE');
        

        $this->addForeignKey('fk-payment_client_by_transfer-version_id', $this->tableOwn,'version_id', $this->tableOwnJournal, 'id','CASCADE','CASCADE');
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-payment_client_by_transfer_history-entity_id',$this->tableOwnJournal);
        $this->dropForeignKey('fk-payment_client_by_transfer_history-creator_id',$this->tableOwnJournal);
        $this->dropForeignKey('fk-payment_client_by_transfer_history-contractor_org',$this->tableOwnJournal);
        $this->dropForeignKey('fk-payment_client_by_transfer_history-client_id',$this->tableOwnJournal);
        
        $this->dropForeignKey('fk-payment_client_by_transfer-version_id',$this->tableOwn);
        
        $this->dropTable($this->tableOwnJournal);

        $this->dropForeignKey('fk-payment_client_by_transfer-contractor_org',$this->tableOwn);
        $this->dropForeignKey('fk-payment_client_by_transfer-client_id',$this->tableOwn);
        $this->dropTable($this->tableOwn);
    }
    
}
