<?php
namespace backend\modules\profiler\models\forms;

use backend\modules\profiler\models;
use yii\base\Model;
use Yii;

class ChangePasswordForm extends Model
{
    public $old_password;
    public $new_password;
    public $new_password_repeat;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['old_password', 'new_password', 'new_password_repeat'], 'filter', 'filter' => 'trim'],
            ['old_password', 'required', 'on' => 'requiredOldPassword'],
            [['new_password', 'new_password_repeat'], 'required'],
            [['old_password', 'new_password', 'new_password_repeat'], 'string', 'min' => 6],
            ['new_password_repeat', 'compare', 'compareAttribute' => 'new_password'],
            ['old_password', 'validatePassword', 'on' => 'requiredOldPassword']
        ];
    }

    public function attributeLabels()
    {
        return [
            'old_password' => Yii::t('profiler', 'OLD_PASSWORD'),
            'new_password' => Yii::t('profiler', 'NEW_PASSWORD'),
            'new_password_repeat' => Yii::t('profiler', 'NEW_PASSWORD_REPEAT'),
        ];
    }

    public function validatePassword($attribute, $params)
    {
        $user = Yii::$app->user->identity;
        if (!$this->hasErrors() && $user->Password != '') {
            if (!$user->validatePassword($this->old_password)) {
                $this->addError($attribute, Yii::t('profiler', 'OLD_PASSWORD_IS_WRONG'));
            }
        }
    }

}
