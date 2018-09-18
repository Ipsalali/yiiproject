<?php
namespace backend\modules\profiler\models\forms;

use yii\base\Model;
use backend\modules\profiler\models\User;
use Yii;

class RetryConfirmEmailForm extends Model
{
    public $email;

    public function rules()
    {
        return [
            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'exist',
                'targetClass' => '\budyaga\users\models\UserEmailConfirmToken',
                'targetAttribute' => 'new_email',
                'filter' => ['old_email' => ''],
                'message' => Yii::t('users', 'USER_WITH_SUCH_EMAIL_DO_NOT_EXISTS')
            ],
        ];
    }

    public function attributeLabels()
    {
        return [
            'email' => Yii::t('profiler', 'EMAIL'),
        ];
    }

    public function getUser()
    {
        return Yii::$app->user->identity::findOne(['email' => $this->email]);
    }
}