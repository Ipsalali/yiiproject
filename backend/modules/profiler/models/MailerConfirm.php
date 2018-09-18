<?php

namespace backend\modules\profiler\models;

use Yii;
use yii\helpers\ArrayHelper;
use yii\base\InvalidParamException;

class MailerConfirm
{

	protected $old_email;
	protected $new_email;

	public function setEmail($email)
    {	
    	$user = Yii::$app->user->identity;
    	$this->old_email = $user->email;
    	$this->new_email = $email;
    	
    	$user->email = $this->new_email;
    	

        if($user->email) {
        	$user->generateEmailConfirmToken();
            if ($this->sendEmailConfirmationMail(Yii::$app->controller->module->getCustomMailView('confirmNewEmail'), 'new_email',$user)) {
                if ($this->sendEmailConfirmationMail(Yii::$app->controller->module->getCustomMailView('confirmChangeEmail'), 'old_email',$user)) {
                    $user->email_confirmed = 0;
                    return $user->save(true);
                }
            }
        }
        return false;
    }


	public function sendEmailConfirmationMail($view, $toAttribute, $user)
    {
        return \Yii::$app->mailer->compose(['html' => $view . '-html', 'text' => $view . '-text'], ['user' => $user])
            ->setFrom([\Yii::$app->params['supportEmail'] => Yii::$app->name . ' robot'])
            ->setTo($this->{$toAttribute})
            ->setSubject('Email confirmation for ' . \Yii::$app->name)
            ->send();
    }




    public static function confirmEmail($token){
        if (empty($token) || !is_string($token)) {
            throw new InvalidParamException('Email confirm token cannot be blank.');
        }
        
        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.tokenExpire'];
        
        $user = false;

        if($timestamp + $expire >= time()){
            $userModel = Yii::$app->user->identity;
            $user = $userModel::findOne([
                'email_token' => $token,
                'status' => $userModel::STATUS_ACTIVE,
            ]);
        }

        
        if (!$user && isset($user->id) && $user->id) {
            throw new InvalidParamException('Wrong email confirm token.');
        }
        
        $user->email_confirmed = true;
        $user->email_token = null;

        return $user->save(true);
    }
}
