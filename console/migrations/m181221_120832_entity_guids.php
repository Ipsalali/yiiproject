<?php

use yii\db\Migration;

/**
 * Class m181221_120832_entity_guids
 */
class m181221_120832_entity_guids extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn("{{%autotruck}}",'guid',$this->string()->null());
        $this->addColumn("{{%autotruck}}",'doc_number',$this->string()->null());
        $this->addColumn("{{%user}}",'guid',$this->string()->null());
        $this->addColumn("{{%client}}",'guid',$this->string()->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn("{{%client}}",'guid');
        $this->dropColumn("{{%user}}",'guid');
        $this->dropColumn("{{%autotruck}}",'doc_number');
        $this->dropColumn("{{%autotruck}}",'guid');
    }

}
