<?php

use yii\db\Migration;

/**
 * Class m181222_092705_set_invoice_autotruck
 */
class m181222_092705_set_invoice_autotruck extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {   
        $sql = "UPDATE autotruck SET invoice = name WHERE id > 0";
        Yii::$app->db->createCommand($sql)->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $sql = "UPDATE autotruck SET invoice = NULL WHERE id > 0";
        Yii::$app->db->createCommand($sql)->execute();
    }


}
