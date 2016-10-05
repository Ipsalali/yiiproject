<?php
namespace common\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use common\models\Post;
use common\models\Client;
use developeruz\db_rbac\interfaces\UserRbacInterface;
use yii\db\Query;

/**
 * User model
 *
 * @property integer $id
 * @property string $username
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property string $auth_key
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $password write-only password
 */
class User extends ActiveRecord implements IdentityInterface, UserRbacInterface
{
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 10;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            
            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['email', 'unique', 'targetClass' => '\common\models\User', 'message' => 'This email address has already been taken.'],

            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_DELETED]],
        ];
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByEmail($email)
    {
        return static::findOne(['email' => $email, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return boolean
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    public function getPost(){
        return $this->hasOne(Post::className(),['creator'=>'id']);
    }

    public function getPosts(){
        return Post::find()->where('creator = '.$this->id)->all();
    }

    public function getClient(){
        return $this->hasOne(Client::className(),['user_id'=>'id']);
    }

    public function getRole(){

        $role = Yii::$app->authManager->getRolesByUser($this->id);
        foreach ($role as $key => $obj) {
            if(is_object($obj)) return $obj;
        }

        return null;
    }

    public function getUserName()
    {
       return $this->username;
    }

    public function getName()
    {
       return $this->name;
    }

    public function getPhone()
    {
       return $this->phone;
    }


    public static function getManagers(){
        $manager = Yii::$app->authManager->getRole('client_manager');
        
        $query = new Query;
        $managers = $query->select('user_id')->from("auth_assignment")->where('`item_name` IN ("'.$manager->name.'")')->all();
        if(count($managers)){
            $in = '';
            $last = array_pop($managers);
            foreach ($managers as $key => $m) {
                $in .= $m['user_id'].',';
            }
            $in .= $last['user_id'];

            return self::find()->where(' id IN ('.$in.')')->all();
        }
       return array();
    }

    //Возвращает менеджеры для расхода
    public static function getExpensesManagers(){
        $expmanager[] = "'".Yii::$app->authManager->getRole('client_manager')->name."'";
        $expmanager[] = "'".Yii::$app->authManager->getRole('main_manager')->name."'";
        $expmanager[] = "'".Yii::$app->authManager->getRole('App_manager')->name."'";
        $expmanager[] = "'".Yii::$app->authManager->getRole('expenses_manager')->name."'";
        $expmanager[] = "'".Yii::$app->authManager->getRole('seller')->name."'";
        $expm = implode(",", $expmanager);
        $query = new Query;
        
        $managers = $query->select('user_id')->from("auth_assignment")->where('`item_name` IN ('.$expm.')')->all();
        if(count($managers)){
            $in = '';
            $last = array_pop($managers);
            foreach ($managers as $key => $m) {
                $in .= $m['user_id'].',';
            }
            $in .= $last['user_id'];

            return self::find()->where(' id IN ('.$in.')')->all();
        }


       return array();
    } 

    public function afterDelete(){
        if($this->client) $this->client->delete();

        // if($this->posts){
        //     foreach ($this->posts as $key => $post) {
        //         if(is_object($post)) $post->delete(); 
        //     }
        // }



        parent::afterDelete();
    }
}
