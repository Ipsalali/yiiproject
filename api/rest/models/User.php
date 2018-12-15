<?php
namespace api\rest\models;

use Yii;
use yii\base\NotSupportedException;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use yii\db\Query;
use common\models\User as cUser;

class User extends cUser{
    
    
    
    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {   
        return static::find()
            ->joinWith('tokens t')
            ->andWhere(['t.token' => $token])
            ->andWhere(['>', 't.expired_at', time()])
            // ->andWhere(['>', 't.expired_at', time()])
            ->one();
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTokens()
    {
        return $this->hasMany(Token::className(), ['user_id' => 'id']);
    }



    public function fields()
    {
        return [
            'id' => 'id',
            'username' => 'username',
            'email' => 'email',
            'phone' => 'phone',
        ];
    }
}
