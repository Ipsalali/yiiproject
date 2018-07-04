<?php
namespace frontend\models;

use common\models\User;
use yii\base\Model;
use frontend\helpers\Mail;

/**
 * Password reset request form
 */
class PasswordResetRequestForm extends Model
{
    public $email;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'exist',
                'targetClass' => '\common\models\User',
                'filter' => ['status' => User::STATUS_ACTIVE],
                'message' => 'Пользователя с таким E-mail адресом не найден.'
            ],
        ];
    }

    /**
     * Sends an email with a link, for resetting the password.
     *
     * @return boolean whether the email was send
     */
    public function sendEmail()
    {
        /* @var $user User */
        $user = User::findOne([
            'status' => User::STATUS_ACTIVE,
            'email' => $this->email,
        ]);
		
		
		
        if ($user) {
            if (!User::isPasswordResetTokenValid($user->password_reset_token)) {
                $user->generatePasswordResetToken();
            }
			
			
			
            if ($user->save()) {
				
                return \Yii::$app->mailer->compose(['html' => 'passwordResetToken-html', 'text' => 'passwordResetToken-text'], ['user' => $user])
                    ->setFrom([\Yii::$app->params['supportEmail'] => \Yii::$app->name . ' robot'])
                    ->setTo($this->email)
                    ->setSubject('Восстановление пароля для ')
                    ->send();
            }
        }

        return false;
    }
	
	function MailSend($tomail,$html,$files=null){
            
            $sender="Восстановление пароля";
            $subject="Восстановление пароля";
            $text="Восстановление пароля";

            $mail = new Mail();

            $mail->protocol = 'mail';
            $mail->parameter = '';
            $mail->hostname = '';
            $mail->username = '';
            $mail->password = '';
            $mail->port = 25;
            $mail->timeout = 5;
            
            
            $mail->setTo($tomail);
            $mail->setFrom("magomedaliev.93@mail.ru");
            $mail->setSender($sender);
            $mail->setSubject(html_entity_decode($subject, ENT_QUOTES, 'UTF-8'));
            $mail->setHtml($html);
            $mail->setText(html_entity_decode($text, ENT_QUOTES, 'UTF-8'));
            $mail->setAttachment($files);
            $mail->send();
            
            return 1;//$mail->send();
    }
}
