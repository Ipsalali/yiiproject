<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Expression;
use common\models\User;
use frontend\models\App;
use frontend\models\Autotruck;

/**
*
*
*
*/

class Client extends ActiveRecord
{


	public function rules(){
		return [
            // name, email, subject and body are required
            [['name'], 'required']
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
		return '{{%client}}';
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
    		'id'=>'Id',
    		'name'=>'Имя',
    		'last_name'=>'Фамилия',
    		'phone'=>'Телефон',
            'user_id'=>'Профиль'
    		);
    }


    public function getUser(){
        return $this->hasOne(User::className(),["id"=>'user_id']);
    }

    public function getName()
    {
       return $this->name;
    }

    public function getLast_name()
    {
       return $this->last_name;
    }

    public function getPhone()
    {
       return $this->phone;
    }


    public function getApps(){
        return App::find()->where("client=".$this->id)->all();
    }

    //Возвращает наименования сгруппированные по заявкам
    public function getAppsSortAutotruck(){
        $apps = $this->apps;
        $sorted =array(); 
        if($apps){

            foreach ($apps as $key => $a) {
                $sorted[$a->autotruck_id]['apps'][] = $a;
            }
        }

        return $sorted;
    }


}