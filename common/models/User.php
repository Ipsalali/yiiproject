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
use frontend\models\ExpensesManager;
use frontend\models\PaymentsExpenses;

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
            ['email', 'unique', 'targetClass' => '\common\models\User', 'message' => 'Такой email уже используется'],
            ['username', 'unique', 'targetClass' => '\common\models\User', 'message' => 'Такой логин уже используется'],

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

    public function getAccessCountry(){
        $query = new Query;
        return $query->select("country_id")->from("manager_country")->where(['user_id'=>$this->id])->all();

    }


    public function getCountries(){
        $countries = $this->getAccessCountry();

        if(is_array($countries) && count($countries)){
            $ids = \yii\helpers\ArrayHelper::map($countries,'country_id','country_id');
            return \common\models\SupplierCountry::find()->where(['in','id',$ids])->all();
        }

        return [];
    }



    public function addAccessCountry($Country){

        $this->removeAccessCountry();
        if(is_array($Country) && count($Country)){
            $insert = [];
            $values['user_id']=$this->id;    
            foreach ($Country as $key => $c) {
                $values['country_id']=$c;  
                array_push($insert, $values);
            }

            return Yii::$app->db->createCommand()->batchInsert("manager_country", ['user_id','country_id'], $insert)
                   ->execute();
        }
    }

    public function removeAccessCountry(){

        return \Yii::$app->db->createCommand()->delete("manager_country",['user_id'=>$this->id])->execute();
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
    public static function getExpensesManagers($search_key = false){
        
        $expmanager[] = "'".Yii::$app->authManager->getRole('client_manager')->name."'";
        
        $expmanager[] = "'".Yii::$app->authManager->getRole('clientExtended')->name."'";
        $expmanager[] = "'".Yii::$app->authManager->getRole('client')->name."'";
        
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

            if($search_key){
                return self::find()->select(['user.id','user.`name`','user.username','user.email','user.phone','cl.name','cl.full_name'])->leftJoin(['cl'=>Client::tableName()],"cl.`user_id` = user.`id`")->where(' user.id IN ('.$in.')')->andWhere("(cl.`email` like '{$search_key}%' or user.`email` like '{$search_key}%' or `username` like '{$search_key}%' or cl.`name` like '{$search_key}%' or cl.`full_name` like '{$search_key}%')")->all();
            }else{
                return self::find()->where(' id IN ('.$in.')')->all();
            }
        }


       return array();
    }
    
     //Возвращает менеджеры для расхода
    public static function getSellers($search_key = false){
        
        
        $expmanager = Yii::$app->authManager->getRole('seller')->name;
        
        $query = new Query;
        
        $managers = $query->select('user_id')->from("auth_assignment")->where(['item_name'=>$expmanager])->all();
        if(count($managers)){
            $in = '';
            $last = array_pop($managers);
            foreach ($managers as $key => $m) {
                $in .= $m['user_id'].',';
            }
            $in .= $last['user_id'];

            if($search_key){
                return self::find()->select(['user.id','user.`name`','user.username','user.email','user.phone','cl.name','cl.full_name'])->leftJoin(['cl'=>Client::tableName()],"cl.`user_id` = user.`id`")->where(' user.id IN ('.$in.')')->andWhere("(cl.`email` like '{$search_key}%' or user.`email` like '{$search_key}%' or `username` like '{$search_key}%' or cl.`name` like '{$search_key}%' or cl.`full_name` like '{$search_key}%')")->all();
            }else{
                return self::find()->where(' id IN ('.$in.')')->all();
            }
        }


       return array();
    }


    public function isClient(){
        return $this->hasRole("client");
    }
    
    
    public function isOnlySeller(){
        
        $roles = \Yii::$app->authManager->getRolesByUser($this->id);
        
        return is_array($roles) && count($roles) == 1 && array_key_exists("seller",$roles);
    }
    
    
    public function isSeller($only = false){
        
        if($only){
            return $this->isOnlySeller();
        }else{
            return $this->hasRole("seller");
        }
        
    }


    public function hasRole($role){
        if(!$role) return false;

        return Yii::$app->authManager->checkAccess($this->id,$role);
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

    public function getExpenses($start,$end){
        if(!$start || !$end) return null;

        $start = date("Y.m.d H:i:s",strtotime($start));
        $end = date("Y.m.d H:i:s",strtotime($end));
        

        return ExpensesManager::find()->where("'{$start}'<=`date` AND `date`<='{$end}' AND `manager_id` = ".$this->id)->all();

    }

    public function getPayments($start,$end){
        if(!$start || !$end) return null;

        $start = date("Y.m.d H:i:s",strtotime($start));
        $end = date("Y.m.d H:i:s",strtotime($end));
        
        return PaymentsExpenses::find()->where("'{$start}'<=`date` AND `date`<='{$end}' AND `manager_id` = ".$this->id)->all();

    }

    public function getPaymentsAndExpenses($start,$end){
        if(!$start || !$end) return null;

        $start = date("Y.m.d H:i:s",strtotime($start));
        $end = date("Y.m.d H:i:s",strtotime($end));
        
        $sql = "SELECT ex.id,manager_id,ex.date,ROUND(ex.cost,2) as sum, 0 as sum_cash_us,0 as sum_cash,0 as sum_card,ex.comment, 0 as type,0 as plus, 0 as toreport,0 as course
                FROM expenses_manager ex
                inner join autotruck as a on a.id = ex.autotruck_id

                WHERE '{$start}'<=ex.`date` AND   ex.`date`<='{$end}' AND ex.`manager_id` = ".$this->id." 
                
                
                UNION ALL
                SELECT pe.id,manager_id,pe.date,ROUND(pe.sum,2) as sum,sum_cash_us, sum_cash,sum_card, pe.comment, 1 as type,plus,toreport,pe.course
                FROM payments_expenses pe 
                WHERE '{$start}'<=`date` AND `date`<='{$end}' AND `manager_id` = ".$this->id."
                ";


        $role_name = $this->getRole()->name;
        if($role_name == "client" || $role_name == "clientExtended"){
            $sql .= "UNION ALL
                    SELECT at.`id`,a.`client` as 'manager_id',apt.`trace_date` as 'date',Round(SUM(a.`summa_us`),2) as 'sum',0 as sum_cash_us, truncate(SUM(a.`summa_us`) * at.course,2) as sum_cash,truncate(SUM(a.`summa_us`) * at.course + (SUM(a.`summa_us`) * at.course*c.payment_clearing/100),2) as sum_card,a.comment,2 as type,0 as plus,0 as toreport,at.course
                    FROM autotruck at
                    INNER JOIN app a ON a.autotruck_id = at.id
                    INNER JOIN client c ON c.id = a.client
                    INNER JOIN app_status a_s ON a_s.id = at.status
                    INNER JOIN app_trace apt ON apt.autotruck_id = at.id
                    WHERE  a_s.send_check = 1 AND apt.status_id = at.status AND '{$start}'<= apt.`trace_date` AND apt.`trace_date`<='{$end}' AND c.`user_id` = ".$this->id." GROUP BY at.id";
         
        }

        $sql .= " order by date";
        
        $connection = Yii::$app->getDb();
        $command = $connection->createCommand($sql);

        $report = $command->queryAll();
        
        return $report;
    }


    public function getManagerSverka($withCourse = false,$endDate = null){
        $endDate = !$endDate ? date("Y-m-d",time()) : $endDate;

        $sql = "CALL `get_user_sverka`({$this->id},'{$endDate}')";
        $connection = Yii::$app->getDb();
        $command = $connection->createCommand($sql);
        $row = $command->queryOne();

        return $withCourse ? $row : $row['sum'];
    }



    // public function getManagerSverka($withCourse = false,$endDate = null){
        
        
    //     $endDate = !$endDate ? date("Y-m-d",time()) : $endDate;
        
    //     if(!isset($this->id)) return null;
    //     $role_name = $this->getRole()->name;
    //     if(($role_name == "client" || $role_name == "clientExtended")){
            
    //         //Считаем сверку до конечной указанной дате
    //         $innerAppTrace = $endDate ? "INNER JOIN app_trace apt ON apt.autotruck_id = at.id" : "";
    //         $dataCondition = $endDate ? " AND apt.status_id = at.status  AND apt.`trace_date` <= '".date("Y-m-d",strtotime($endDate))."' " : "";
            
    //         $sql_client = "UNION ALL
    //                 SELECT ROUND(SUM(a.`summa_us`),2) as 'sum', SUM(a.`summa_us` * at.`course`) as 'sum_cash', SUM((c.payment_clearing/100 * a.`summa_us` * at.`course`) + (a.`summa_us` * at.`course`)) as sum_card
    //                 FROM autotruck at
    //                 INNER JOIN app a ON a.autotruck_id = at.id
    //                 INNER JOIN client c ON c.id = a.client
    //                 INNER JOIN app_status a_s ON a_s.id = at.status
    //                 ".$innerAppTrace."
    //                 WHERE  c.`user_id` = ".$this->id." AND a_s.send_check = 1 ".$dataCondition;
            
    //     }else{
    //         $sql_client = "";
    //     }

    //     $dataConditionExp = $endDate ? " AND  ex.`date` <= '".date("Y-m-d",strtotime($endDate))."' " : "";
    //     $dataConditionPay = $endDate ? " AND  pe.`date` <= '".date("Y-m-d",strtotime($endDate))."' " : "";
        
    //     $sql = "SELECT ROUND(SUM( sum ),2) as sum, ROUND(SUM( sum_cash ),2) as sum_cash, ROUND(SUM( sum_card ),2) as sum_card
    //             FROM (
    //                 SELECT SUM( ex.cost ) AS sum, 0 as sum_cash, 0 as sum_card
    //                 FROM expenses_manager ex
    //                 WHERE  `manager_id` = ".$this->id." ".$dataConditionExp." 
    //                 UNION ALL 
    //                 SELECT SUM( if(1,0 - pe.sum,pe.sum) ) AS sum, SUM(if(1,0-pe.sum_cash,pe.sum_cash)) as sum_cash, SUM(if(1,0-pe.sum_card,pe.sum_card)) as sum_card
    //                 FROM payments_expenses pe
    //                 WHERE  `manager_id` = ".$this->id." ".$dataConditionPay." 
    //                 ".$sql_client."
    //             ) AS v";

    //     $connection = Yii::$app->getDb();
    //     $command = $connection->createCommand($sql);
    //     $row = $command->queryOne();
        
    //     return $withCourse ? $row : $row['sum'];
    // }



    public static function getUnAssignedUserForClient(){
        $sql = "SELECT `id`,`username`,`email`,`name`,`phone` FROM ".self::tableName()." as u
                INNER JOIN `auth_assignment` as aa ON u.`id` = aa.`user_id`
                WHERE not exists (SELECT c.`id` from ".Client::tableName()." as c WHERE c.`user_id`=u.`id`)  and aa.item_name = 'client'";

        $connection = Yii::$app->getDb();
        $command = $connection->createCommand($sql);
        return $command->queryAll();
    }
}
