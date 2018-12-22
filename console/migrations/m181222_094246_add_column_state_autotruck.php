<?php

use yii\db\Migration;
use common\dictionaries\AutotruckState;
/**
 * Class m181222_094246_add_column_state_autotruck
 */
class m181222_094246_add_column_state_autotruck extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn("{{%autotruck}}",'state',$this->string()->null()->defaultValue(AutotruckState::CREATED));
        $this->addColumn("{{%autotruck_history}}",'state',$this->string()->null()->defaultValue(AutotruckState::CREATED));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn("{{%autotruck}}",'state');
        $this->dropColumn("{{%autotruck_history}}",'state');
    }
}
