<?php

use yii\db\Migration;

/**
 * Class m181228_115219_user_current_actions
 */
class m181228_115219_user_current_actions extends Migration
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
        $this->createTable('{{%user_actions}}', [
            'id' => $this->primaryKey(),
            'table_name' => $this->string()->notNull(),
            'record_id' => $this->integer()->notNull(),
            'user_id' => $this->integer()->notNull(),
            'start_at' => $this->timestamp()->null(),
            'finish_at' => $this->timestamp()->null(),
            'event' => $this->string()->notNull(),
            'active' => $this->smallInteger()->notNull()->defaultValue(1)
        ], $tableOptions);
        $this->createIndex('idx-user_actions-user_id', '{{%user_actions}}', 'user_id');
        $this->addForeignKey('fk-user_actions-user_id', '{{%user_actions}}', 'user_id', '{{%user}}', 'id', 'CASCADE', 'RESTRICT');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%user_actions}}');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m181228_115219_user_current_actions cannot be reverted.\n";

        return false;
    }
    */
}
