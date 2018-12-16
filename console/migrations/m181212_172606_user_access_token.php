<?php

use yii\db\Migration;

/**
 * Class m181212_172606_user_access_token
 */
class m181212_172606_user_access_token extends Migration
{


    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%token}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'token' => $this->string()->notNull()->unique(),
            'is_active' => $this->boolean()->null()->defaultValue(1),
            'expired_at' => $this->integer()->notNull(),
        ], $tableOptions);
        $this->createIndex('idx-token-user_id', '{{%token}}', 'user_id');
        $this->addForeignKey('fk-token-user_id', '{{%token}}', 'user_id', '{{%user}}', 'id', 'CASCADE', 'RESTRICT');
    }



    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%token}}');
    }

}
