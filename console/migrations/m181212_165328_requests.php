<?php

use yii\db\Migration;

/**
 * Class m181212_165328_requests
 */
class m181212_165328_requests extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }


        $this->createTable('{{%requests}}', [
            'id' => $this->primaryKey(),
            'created_at'=>$this->timestamp(),
            'completed_at'=>$this->timestamp(),
            'request'=>$this->string(50)->notNull(),
            'params_in'=>$this->text(50)->null(),
            'params_out'=>$this->text(50)->null(),
            'result'=>$this->boolean()->null()->defaultValue(0),
            'completed'=>$this->boolean()->null()->defaultValue(0),
            'autotruck_id'=>$this->integer()->null(),
            'user_id'=> $this->integer()->null(),
            'actor_id'=> $this->integer()->null()
        ], $tableOptions);


        $this->addForeignKey('fk-requests-autotruck_id',"{{%requests}}",'autotruck_id',"{{%autotruck}}",'id','CASCADE','CASCADE');
        $this->addForeignKey('fk-requests-user_id',"{{%requests}}",'actor_id',"{{%user}}",'id','CASCADE','CASCADE');
        $this->addForeignKey('fk-requests-actor_id',"{{%requests}}",'actor_id',"{{%user}}",'id','CASCADE','CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-requests-actor_id',"{{%requests}}");
        $this->dropForeignKey('fk-requests-user_id',"{{%requests}}");
        $this->dropForeignKey('fk-requests-autotruck_id',"{{%requests}}");
        $this->dropTable('{{%requests}}');
    }
}
