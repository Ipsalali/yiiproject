<?php

namespace common\models;

use yii\db\ActiveRecord;
use common\helper\ExcelAutotruckParser;

class AutotruckImport extends ActiveRecord{

	public $file;

	public function rules(){
		return [
			[['fileBinary','extension'],'required'],
			['fileBinary','safe'],
			['name','string'],
			['extension','string'],
			['name','default','value'=>null],
			['creator','default','value'=>\Yii::$app->user->getId()],
			['isDeleted','default','value'=>0],
			[['file'], 'file', 'skipOnEmpty' => true,'checkExtensionByMimeType'=>false, 'extensions' => 'xls,xlsx'],
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
		return '{{%autotruck_import}}';
	}


	/**
     * @return array primary key of the table
     **/     
    public static function primaryKey(){
    	return array('id');
    }


    public function FileConvertArray(){
    	if(!$this->fileBinary) return [];

    	return ExcelAutotruckParser::parse($this);

    }
}