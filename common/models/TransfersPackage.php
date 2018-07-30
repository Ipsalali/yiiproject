<?php

namespace common\models;

use Yii;
use common\base\ActiveRecordVersionable;
use yii\db\Expression;
use yii\db\Query;
use yii\db\Command;
use common\models\Transfer;
use common\models\SellerExpenses;

class TransfersPackage extends ActiveRecordVersionable
{
    public $tempFiles = "1";

    public static $filesPath = "transfers/";


    const C_DOLLAR = 1;
    const C_EURO = 2;

    const C_DOLLAR_SYMBOL = "$";
    const C_EURO_SYMBOL = "€";

    const S_START = 1;
    const S_END = 2;

    const S_START_TITLE = "Отправлено";
    const S_END_TITLE = "Исполнено";

    protected static $currencyCodeTitle = [
        self::C_DOLLAR => self::C_DOLLAR_SYMBOL,
        self::C_EURO => self::C_EURO_SYMBOL
    ];

    protected static $statusCodeTitle = [
        self::S_START => self::S_START_TITLE,
        self::S_END => self::S_END_TITLE
    ];

    public static function versionableAttributes(){
        return [
            'name',
            'currency',
            'course',
            'status',
            'status_date',
            'date',
            'files',
            'comment',
            'isDeleted',
        ];
    }


    public function storyActions(){

        $a = parent::storyActions();
        $a[4]= "changeStatus";
        $a[5]= "unlinkFile";
        return $a;
    } 

	public function rules(){
		return [
            
            [['name','currency','course','status','status_date','date'],"required"],
            
            [['name','course','comment'],'filter','filter'=>function($v){ return trim(strip_tags($v));}],

            [['course'],'number'],

            ['currency','in','range'=>[self::C_DOLLAR,self::C_EURO]],
            ['status','in','range'=>[self::S_START,self::S_END]],

            [['date','status_date'],'filter','filter'=>function($v){ 
                if(is_integer($v)){
                    return date("Y-m-d H:i:s",$v);
                }else{
                    return date("Y-m-d H:i:s",strtotime($v));
                }
            }],
            [['date','status_date'],'default','value'=>date("Y-m-d H:i:s",time())],

            ['isDeleted','default','value'=>0],

            [['files'], 'file', 'skipOnEmpty' => true,'checkExtensionByMimeType'=>false, 'extensions' => 'xls,xlsx,doc,docx,pdf,jpeg,jpg,png','maxFiles'=>20],
            ['files','default','value'=>null]
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
		return '{{%transfers_package}}';
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
    		'name'=>'Название',
            'currency'=>'Валюта',
            'course'=>'Курс',
    	    'status'=>'Статус',
            'status_date'=>'Время действий',
            'date'=>'Дата',
            'files'=>'Файлы',
            'version_id'=>"Версия",
            'comment'=>"Комментарий",
            'isDeleted'=>"В архив"
        );
    }

    
    public function getStatusTitle($code = null){
        $code = !$code ? $this->status : $code;
        if(array_key_exists($code, self::$statusCodeTitle)){
            return self::$statusCodeTitle[$code];
        }else{
            return null;
        }
    }


    public function getCurrencyTitle($code = null){

        $code = !$code ? $this->currency : $code;
        if(array_key_exists($code, self::$currencyCodeTitle)){
            return self::$currencyCodeTitle[$code];
        }else{
            return null;
        }
    }



    public function getStatuses(){
        return [
            [
                'id'=>self::S_START,
                'title'=>self::S_START_TITLE,
            ],
            [
                'id'=>self::S_END,
                'title'=>self::S_END_TITLE,
            ],
        ];
    }



    public function getCurrencies(){
        return [
            [
                'id'=>self::C_DOLLAR,
                'title'=>self::C_DOLLAR_SYMBOL,
            ],
            [
                'id'=>self::C_EURO,
                'title'=>self::C_EURO_SYMBOL,
            ],
        ];
    }





    public function getStatusSory(){
        return (new Query)->from(self::resourceTableName())->where([static::resourceKey()=>$this->id,"type_action"=>4])->orderBy(["id"=>SORT_DESC])->all();
    }





    public function uploadFile()
    {
        if ($this->validate()) {

            if(!file_exists(self::$filesPath)){
                //если директория не существует, создаем директорию
                if(!mkdir(self::$filesPath)){
                    throw new Exception("Не удалось создать директорию для хранения файлов", 1);
                }
            }

            $files = explode('|', $this->tempFiles);
            
            foreach ($this->files as $key => $file) {
                $basename = $file->basename;
                $fName = $basename . '_'.time().'.' . $file->extension;
                $file->saveAs(self::$filesPath . $fName);
                $files[] = $fName;
            }
            
            return implode('|', $files);
        } else {
            return false;
        }

    }


    public function unlinkFile($file = null){
       
        if(!$file || !file_exists(self::$filesPath.$file)) return null;
    

        if($this->files != ''){

            $files = explode('|', $this->files);
            if(count($files)){
                $new_files = array();
                foreach($files as $key => $item) {
                    if($file == $item){
                        if(file_exists(self::$filesPath.$file)){
                            unlink(self::$filesPath.$file);
                        }
                    }else{
                        $new_files[] = $item;
                    }
                }

                $this->files = implode('|', $new_files);
                $this->setStoryAttributeTypeAction(5);
                return $this->save(1);
            }
        }
    }


    public function fileExists($file){
        if(!$file || !$this->files) return null;

        $files = explode("|", $this->files);
        if(count($files)){
            return in_array($file,$files);
        }
        return 0;
    }


    public function getTransfers(){
        if(!$this->id) return [];

        return Transfer::find()->where(['package_id'=>$this->id,'isDeleted'=>0])->all();
    }
    
    
    
    public function getSellerExpenses(){
        if(!$this->id) return [];

        return SellerExpenses::find()->where(['package_id'=>$this->id,'isDeleted'=>0])->all();
    }


    /**
    *
    * return array - модели с ошибками
    * return false - transfers are empty
    * return true - все ок
    * return 2 - не все услуги были добавлены status warning
    */
    public function saveTransfers($transfers){
        if(!is_array($transfers) || !count($transfers)) return false;

        //производим проверку на валидность
        $errors = [];
        $models = [];
        foreach ($transfers as $key => $t) {
            if(isset($t['id']) && (int)$t['id']){
                $model = Transfer::findOne((int)$t['id']);

                if(!isset($model->id) || !$model->id){
                    $model = new Transfer();
                }
            }else{
                $model = new Transfer();
            }

            $t['package_id'] = $this->id;
            $data = ['Transfer'=>$t];
            if(!$model->load($data) || !$model->validate()){
                array_push($errors, $model);
            }
            
            array_push($models, $model);
        }



        if(!count($errors)){

            $answer = true; 
            foreach ($models as $key => $m) {
                if(!$m->save(1)){
                    $answer = 2;
                }
            }

            return $answer;

        }else{
            return $models;
        }
    }
    
    
    /**
    *
    * return array - модели с ошибками
    * return false - expenses are empty
    * return true - все ок
    * return 2 - не все услуги были добавлены status warning
    */
    public function saveExpenses($expenses){
        if(!is_array($expenses) || !count($expenses)) return false;

        //производим проверку на валидность
        $errors = [];
        $models = [];
        foreach ($expenses as $key => $t) {
            if(isset($t['id']) && (int)$t['id']){
                $model = SellerExpenses::findOne((int)$t['id']);

                if(!isset($model->id) || !$model->id){
                    $model = new SellerExpenses();
                }
            }else{
                $model = new SellerExpenses();
            }

            $t['package_id'] = $this->id;
            $data = ['SellerExpenses'=>$t];
            if(!$model->load($data) || !$model->validate()){
                array_push($errors, $model);
            }
            
            array_push($models, $model);
        }

        if(!count($errors)){

            $answer = true; 
            foreach ($models as $key => $m) {
                if(!$m->save(1)){
                    $answer = 2;
                }
            }

            return $answer;

        }else{
            return $models;
        }
    }

}