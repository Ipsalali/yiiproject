<?php
namespace frontend\models;

use common\models\User;
use yii\base\Model;
use Yii;

/**
 * Signup form
 */
class SignupForm extends Model
{
    public $username;
    public $email;
    public $phone;
    public $password;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['email','phone'], 'filter', 'filter' => 'trim'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['email', 'unique', 'targetClass' => '\common\models\User', 'message' => 'Такой email уже используется'],
            
            ['username', 'default', 'value'=>null],
            ['email','default','value'=>null],
            ['phone','default','value'=>null],

            ['password', 'required'],
            ['password', 'string', 'min' => 6],
        ];
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels(){
        return array(
            'email'=>'E-mail',
            'phone'=>'Номер телефона',
            'username'=>'Имя',
            'password'=>'Пароль'
        );
    }

    public function load($data, $formName = null){

        if(parent::load($data, $formName)){

            if(!$this->phone && !$this->email){
                $this->addError('phone',"Не заполнено обязательное поле(email или номер).");
                return false;
            }

            return true;
        }

        return false;
    }


    /**
     * Signs user up.
     *
     * @return User|null the saved model or null if saving fails
     */
    public function signup()
    {
        if ($this->validate()) {
            $user = new User();
            $user->username = $this->username;
            $user->email = $this->email;
            $user->phone = $this->phone;
            $user->setPassword($this->password);
            $user->generateAuthKey();
            if ($user->save(1)) {
                return $user;
            }
        }

        return null;
    }
}
