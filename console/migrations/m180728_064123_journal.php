<?php

use yii\db\Migration;

class m180728_064123_journal extends Migration
{
    
    public $tableUser = "{{%user}}";
    public $tableUserJournal = "{{%user_history}}";
    
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable($this->tableUserJournal, [
            'id' => $this->primaryKey(),
            'entity_id' => $this->integer()->notNull(),
            
            'username' => $this->string()->null(),
            'auth_key' => $this->string()->null(),
            'password_hash' => $this->string()->null(),
            'password_reset_token' => $this->string()->null(),
            'email' => $this->string()->notNull(),
            'status' => $this->smallInteger()->notNull()->defaultValue(10),
            'phone' => $this->string()->null(),
            'name' => $this->string()->null(),

            'created_at'=>$this->timestamp(),
            'type_action'=> $this->integer()->notNull(),
            'version'=> $this->integer()->notNull(),
            'creator_id'=> $this->integer()->null(),
            'isDeleted'=> $this->smallInteger()->null()->defaultValue(0),
        ], $tableOptions);


        $this->addForeignKey('fk-user_history-entity_id', $this->tableUserJournal,'entity_id', $this->tableUser, 'id','CASCADE','CASCADE');
        $this->addForeignKey('fk-user_history-creator_id', $this->tableUserJournal,'creator_id', $this->tableUser, 'id','CASCADE','CASCADE');

        $this->addColumn($this->tableUser,'version_id',$this->integer()->null());
        $this->addColumn($this->tableUser,'isDeleted',$this->smallInteger()->null()->defaultValue(0));

        $this->addForeignKey('fk-user-version_id', $this->tableUser,'version_id', $this->tableUserJournal, 'id','CASCADE','CASCADE');
    }

    public function safeDown()
    {   
        $this->dropForeignKey('fk-user_history-entity_id',$this->tableUserJournal);
        $this->dropForeignKey('fk-user_history-creator_id',$this->tableUserJournal);

        $this->dropForeignKey('fk-user-version_id',$this->tableUser);
        
        $this->dropColumn($this->tableUser,'version_id');
        $this->dropColumn($this->tableUser,'isDeleted');

        $this->dropTable($this->tableUserJournal);
    }
    
}
