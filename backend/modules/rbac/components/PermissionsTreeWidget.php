<?php

namespace backend\modules\rbac\components;

use backend\modules\rbac\models\AuthItem;
use backend\modules\rbac\models\User;
use yii\base\Widget;
use Yii;

class PermissionsTreeWidget extends Widget
{
    public $user = false;

    public $item = false;

    public function run()
    {   

        $model = null;
        if ($this->user) {

            $userModel = new User($this->user);
            if(isset($userModel->user->id)){
                $model = $userModel->user;
                $assignedPermissions = $userModel->assignedRules;
                $defaultPermissions = AuthItem::find()->where(['in', 'name', Yii::$app->authManager->defaultRoles])->all();
                

                $permissions = array_merge($assignedPermissions, $defaultPermissions);
            }else{
                $permissions = $this->item->children;
            }
            
        } else {
            $permissions = $this->item->children;
        }

        return $this->render('permissionsTreeWidget', ['permissions' => $permissions,'model'=>$model]);
    }
}