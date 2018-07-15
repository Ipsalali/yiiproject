<?php

use yii\db\Migration;

/**
 * Class m180711_152926_sverka
 */
class m180630_080931_usersverka extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%user_sverka}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull()->unique(),
            'sum' => $this->double(),
            'sum_card' => $this->double(),
            'sum_cash' => $this->double(),
            'updated_at' => $this->timestamp()->notNull(),
        ], $tableOptions);

        $this->addForeignKey('fk-sverka-user_id', '{{%user_sverka}}', 'user_id', 'user', 'id','CASCADE','CASCADE');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        
        $this->dropForeignKey('fk-sverka-user_id','{{%user_sverka}}');
        $this->dropTable('{{%user_sverka}}');
        
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180711_152926_sverka cannot be reverted.\n";

        return false;
    }
    */
}
