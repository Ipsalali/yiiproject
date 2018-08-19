<?php

use yii\db\Migration;

use console\data\Roles;
use console\data\Permissions;

class m180819_093744_roles_and_perms extends Migration
{
    

    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
        $roles = Roles::getRoles();

        if(is_array($roles)){
            $sqls = [];
            foreach ($roles as $name => $desc) {
                $sqls[] = "
                    INSERT INTO {{%auth_item}} SET `name`='{$name}', `type`=1, `description`='{$desc}'
                    ON DUPLICATE KEY UPDATE `name`='{$name}', `type`=1, `description`='{$desc}';
                ";
            }

            if(count($sqls)){
                $query = implode("", $sqls);
                $this->execute($query);
            }
        }



        $perms = Permissions::getPerms();

        if(is_array($perms)){
            $sqls = [];
            foreach ($perms as $name => $desc) {
                $sqls[] = "
                    INSERT INTO {{%auth_item}} SET `name`='{$name}', `type`=2, `description`='{$desc}'
                    ON DUPLICATE KEY UPDATE `name`='{$name}', `type`=2, `description`='{$desc}';
                ";
            }

            if(count($sqls)){
                $query = implode("", $sqls);
                $this->execute($query);
            }
        }
    }

    public function safeDown()
    {

    }
    
}
