<?php

namespace backend\modules\rbac\models;

use yii\base\BaseObject;
use yii\helpers\ArrayHelper;


class User extends BaseObject
{

    private static $_userModel;

    private  $_user;

    public function __construct($id = null){

        $UserModel = get_class(\Yii::$app->user->identity);
        
        self::$_userModel = new $UserModel;

        if($id){
            $this->_user = $UserModel::findOne($id);
        }
    } 

    public function getUser(){
        return $this->_user;
    }

    public function getAssignments()
    {
        return $this->_user->hasMany(AuthAssignment::className(), ['user_id' => 'id'])->all();
    }

    public function getAssignedRules()
    {
        return $this->_user->hasMany(AuthItem::className(), ['name' => 'item_name'])->viaTable(AuthAssignment::tableName(), ['user_id' => 'id'])->all();
    }



    public function getNotAssignedRules(){
        return AuthItem::find()->where(['not in', 'name', ArrayHelper::getColumn($this->assignedRules, 'name')])->all();
    }
}
