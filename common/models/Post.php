<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Expression;
use common\models\User;

/**
*
*
*
*/

class Post extends ActiveRecord
{


	public function rules(){
		return [
            // name, email, subject and body are required
            [['title', 'content'], 'required']
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
		return '{{%post}}';
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
    		'title'=>'Title',
    		'content'=>'Content',
    		'created'=>'Created',
    		'updated'=>'Updated',
    		'creator'=>'creator'
    		);
    }


    public function beforeSave($insert)
	{
    	if ($this->isNewRecord)
    	{	
    		$this->created = new Expression('NOW()');
            $this->creator = Yii::$app->user->id;
       	 	$command = static::getDb()->createCommand("select max(id) as id from post")->queryAll();
        	$this->id = $command[0]['id'] + 1;
    	}
 		$this->updated = new Expression('NOW()');
    	return parent::beforeSave($insert);
	}

    public function getUser(){
        return $this->hasOne(User::className(),["id"=>'creator']);
    }
}