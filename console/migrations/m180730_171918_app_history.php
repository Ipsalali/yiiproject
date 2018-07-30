<?php

use yii\db\Migration;

class m180730_171918_app_history extends Migration
{
    

    
    public $tableUser = "{{%user}}";
    public $tableOwn = "{{%app}}";
    public $tableOwnJournal = "{{%app_history}}";
    
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        
        $this->createTable($this->tableOwnJournal, [
            'id' => $this->primaryKey(),
            'entity_id' => $this->integer()->notNull(),
            
            'client' => $this->integer()->null(),
            'weight' => $this->double()->null(),
            'rate' => $this->double()->null(),
            'summa_us' => $this->double()->null(),
            'status' => $this->integer()->null(),
            'comment' => $this->text()->null(),
            'info' => $this->text()->null(),
            'autotruck_id' => $this->integer()->null(),
            'type' => $this->char()->null()->defaultValue("0"),
            'out_sock' => $this->smallInteger()->null()->defaultValue(0),
            'sender' => $this->integer()->null(),
            'count_place' => $this->integer()->null(),
            'package' => $this->integer()->null(),

            'created_at'=>$this->timestamp(),
            'type_action'=> $this->integer()->notNull(),
            'version'=> $this->integer()->notNull(),
            'creator_id'=> $this->integer()->null(),
            'isDeleted'=> $this->smallInteger()->null()->defaultValue(0),
        ], $tableOptions);


        $this->addForeignKey('fk-app_history-entity_id', $this->tableOwnJournal,'entity_id', $this->tableOwn, 'id','CASCADE','CASCADE');
        $this->addForeignKey('fk-app_history-creator_id', $this->tableOwnJournal,'creator_id', $this->tableUser, 'id','CASCADE','CASCADE');

        $this->addColumn($this->tableOwn,'version_id',$this->integer()->null());
        $this->addColumn($this->tableOwn,'isDeleted',$this->smallInteger()->null()->defaultValue(0));

        $this->addForeignKey('fk-app-version_id', $this->tableOwn,'version_id', $this->tableOwnJournal, 'id','CASCADE','CASCADE');
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-app_history-entity_id',$this->tableOwnJournal);
        $this->dropForeignKey('fk-app_history-creator_id',$this->tableOwnJournal);
        
        $this->dropForeignKey('fk-app-version_id',$this->tableOwn);
        
        $this->dropColumn($this->tableOwn,'version_id');
        $this->dropColumn($this->tableOwn,'isDeleted');

        $this->dropTable($this->tableOwnJournal);
    }
    
}
