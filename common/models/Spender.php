<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Expression;
use common\models\Client;
/**
*
*
*
*/

class Spender extends ActiveRecord
{

    const SENDED = 1;
    const UNSENDED = 0;

	public function rules(){
		return [
            // name, email, subject and body are required
            [['from_staff','theme','body'], 'required'],
            ['sended','default','value'=>self::UNSENDED]
        ];
	}


	/**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return Comments the static model class
     */
	public static function model($className = __CLASS__){

		return parent::model($className);
	
	}

	/**
     * @return string the associated database table name
     */

	public static function tableName(){
		return '{{%spender}}';
	}

	/**
     * @return array primary key of the table
     **/     
    public static function primaryKey(){
    	return array('id');
    }


    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels(){
    	return array(
    		'id'=>'Клиент',
    		'from_staff'=>'Адресанта',
            'from_email'=>'E-mail Адресанта',
            'to_email'=>'E-mail Адресата',
            'to_client'=>'Адресат',
            'date'=>'Дата отправления',
            'theme'=>'Тема рассылки',
            'body'=>'Сообщение рассылки',
            'sended'=>'Отправлено'
    		);
    }



    public function load($data,$formName = null){
        if(parent::load($data,$formName)){
            
            $user = Yii::$app->user->identity;
            $this->from_staff = $user->id;
            $this->from_email = $user->email ? $user->email : 'info@tedrans.com';
            
            $validate = false;

            if(isset($data['category']) && is_array($data['category']) && count($data['category'])){
                $ignore = isset($data['ignore_values']) && $data['ignore_values'] !="" ? $data['ignore_values'] : null;
            
                $validate = $this->setToClients($ignore,$data['category']);
            
            }elseif(isset($data['ignore_values']) && $data['ignore_values'] !=""){
                
                $validate = $this->setToClients($data['ignore_values']);
            
            }else{
                
                $validate = $this->setToClients();
            }
            
            $this->date = date("Y-m-d H:i",time());
            $this->sended = self::UNSENDED;
            
            return $validate;
        }else{
            return false;
        }
    }



    protected function setToClients($ignore = null,$category = []){

        $sql = "SELECT user.`email`,client.`id` FROM ".Client::tableName()."
                    INNER JOIN user ON user.`id` = client.`user_id`";
        
        
        $conditions = [];
        
        if($ignore){
            array_push($conditions, "client.`id` NOT IN({$ignore})");
        }

        if(is_array($category) && count($category)){
            array_push($conditions, "client.`client_category_id` IN(".implode(",", $category).")");
        }

        if(count($conditions)){
            $sql .= " WHERE ". implode(" and ", $conditions);
        }
        
        $connection = Yii::$app->getDb();
        $command = $connection->createCommand($sql);
        $res = $command->queryAll();

        $ids = [];
        $emails = [];
        if(is_array($res) && count($res)){
            foreach ($res as $key => $value) {
                array_push($ids, $value['id']);
                array_push($emails, $value['email']);
            }
        }

        $this->to_client = json_encode($ids);
        $this->to_email = json_encode($emails);
        
        return true;
    }


    //Осуществелние рассылки
    public function sendLetter(){

        $data = ["theme"=>$this->theme, "body"=>$this->body];
        
        $message = Yii::$app->mailer->compose('layouts/spender',$data)
                            ->setFrom($this->from_email)
                            ->setSubject($this->theme);

        $emails = json_decode($this->to_email);
        
        $count = 0;
        Yii::$app->db->createCommand('SET SESSION wait_timeout = 28800;')->execute();
        if(is_array($emails) && count($emails)){
            foreach ($emails as $key => $email) {
                if($email){
                    //$email = "magomedaliev.93@mail.ru";    
                    $message->setTo($email);
                    $message->send();
                    
                    /*
                    $count++;
                    
                    if($count >= 30){
                        $count = 0;
                        sleep(30);
                    }*/
                }
            }
            
            $email = "magomedaliev.93@mail.ru";
            $message->setTo($email);
            $message->send();
            
            $sql = "UPDATE ".self::tableName()." SET `sended`= ".self::SENDED." WHERE `id`=".$this->id;
            $connection = Yii::$app->getDb();
            $command = $connection->createCommand($sql);
            $command->execute();
            return 1;
        }else{
            return false;
        }
        

        
    }

}