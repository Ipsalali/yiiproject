<?php
namespace api\rest\models;

use api\rest\models\Token;
use api\rest\models\User;
use yii\base\Model;
/**
 * Auth form
 */
class AuthForm extends Model
{
    public $login;
    public $password;
    private $_user;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['login', 'password'], 'required'],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
        ];
    }
    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Incorrect login or password.');
            }
        }
    }
    

    /**
     * @return Token|null
     */
    public function auth()
    {
        if ($this->validate()) {
            $token = new Token();
            $token->user_id = $this->getUser()->id;
            $token->generateToken(time() + 3600 * 24);
            return $token->save() ? $token : null;
        } else {
            return null;
        }
    }


    /**
     * Finds user by [[login]]
     *
     * @return User|null
     */
    protected function getUser()
    {
        if ($this->_user === null) {
            $this->_user = User::findByLogin($this->login);
        }
        return $this->_user;
    }
}