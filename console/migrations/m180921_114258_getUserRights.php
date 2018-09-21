<?php

use yii\db\Migration;

/**
 * Class m180921_114258_getUserRights
 */
class m180921_114258_getUserRights extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->get_UserRights();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute("Drop procedure if exists `get_UserRights`");
    }



    public function get_UserRights(){

        $sql = <<<SQL

        CREATE PROCEDURE get_UserRights(in p_user_id int)
        BEGIN
    
            SELECT LOWER(item_name) as 'right',user_id 
            FROM auth_assignment WHERE user_id = p_user_id
            UNION ALL
            SELECT LOWER(child) as 'right', user_id
            FROM auth_item_child
            INNER JOIN auth_assignment ON parent = item_name
            WHERE user_id = p_user_id;

        END
SQL;
        $this->execute($sql);

    }
    
}
