<?php
namespace common\models;

use Yii;
use yii\db\{Expression,Query,Command,ActiveRecord};

use soapclient\methods\BaseMethod;

class Request extends ActiveRecord
{   


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['request'], 'required'],

            ['created_at','default','value'=>date("Y-m-d\TH:i:s",time())],
            
            [['completed','result'],'default','value'=>0],
            [['autotruck_id','actor_id','user_id'],'default','value'=>null],
            [['params_out','completed_at','params_in'],'safe']
            
        ];
    }





    public static function tableName(){
        return '{{%requests}}';
    }




    public static function primaryKey(){
        return array('id');
    }




    public function attributeLabels(){
        return array(
            'id'=>'ID',
            'request'=>'Запрос',
            'type'=>'Тип запроса',
            'created_at'=>'Дата инициализации запроса',
            'completed_at' => 'Дата выполнения',
            'params_in'   => 'Входные параметры',
            'params_out' => 'Выходные параметры',
            'result'  => 'Результат',
            'completed' => 'Выполнен',
        );
    }

   
    



    public static function getNextTransactionId(){

        $sql = "SHOW TABLE STATUS WHERE name='requests'";

        $res = Yii::$app->db->createCommand($sql)
            ->queryOne();

       
        if(isset($res['Auto_increment'])){
            return (int)$res['Auto_increment'];
        }else{
            $sql = "SELECT `id` FROM ".self::tableName()." ORDER BY id DESC LIMIT 1";
            $last_id = Yii::$app->db->createCommand($sql)
            //->bindValue(":table",self::tableName())
            ->queryScalar();
            return $last_id+1;
        }

    }





    public function setParamIn($name,$value){

        $params_in = json_decode($this->params_in);
        if(is_object($params_in) && property_exists($params_in, $name)){

            $params_in->$name = $value;
            $params = [];
            foreach ($params_in as $key => $v) {
                $params[$key] = $v;
            }

            $this->params_in = json_encode($params);
            return $this->save();
            
        }
    }



    public function getParamIn($name,$array = false){

        $b = $array && 1;
        $params_in = json_decode($this->params_in,$b);
        
        if(is_object($params_in) && property_exists($params_in, $name)){
            return $params_in->$name;
        }elseif(is_array($params_in) && array_key_exists($name, $params_in)){
            return $params_in[$name];
        }
    }




    public function getParamOut($name,$array = false){
        
        $b = $array && 1;

        $params_out = json_decode($this->params_out,$b);
        if(is_object($params_out) && property_exists($params_out, $name)){
            return $params_out->$name;
        }elseif(is_array($params_out) && array_key_exists($name, $params_out)){
            return $params_out[$name];
        }
    }






    public function send(BaseMethod $method){

        $client = Yii::$app->webservice1C->getClient();
        try {
            ini_set('default_socket_timeout', 600);
            set_time_limit(0);
            if($method->validate()){
                $responce = Yii::$app->webservice1C->send($method);
                Yii::warning(json_encode($responce),"api");
                $responce = json_decode(json_encode($responce),1);
            }else{
                $responce = [
                    'success'=>false,
                    'error'=>'validateError',
                    'errorMessage'=>$method->getErrors()
                ];
            }
            Yii::warning($client->__getLastRequestHeaders(),"webservice");
            Yii::warning($client->__getLastRequest(),"webservice");
            Yii::warning($client->__getLastResponseHeaders(),"webservice");
            Yii::warning($client->__getLastResponse(),"webservice");

        } catch (\SoapFault $e) {

            Yii::warning($client->__getLastRequestHeaders(),"webservice");
            Yii::warning($client->__getLastRequest(),"webservice");
            Yii::warning($client->__getLastResponseHeaders(),"webservice");
            Yii::warning($client->__getLastResponse(),"webservice");

            $responce = [
                'success'=>false,
                'error'=>'SoapFault',
                'errorMessage'=>$e->getMessage()
            ];
        
        } catch(\Exception $e){
           
            $responce = [
                'success'=>false,
                'error'=>'SiteError',
                'errorMessage'=>$e->getMessage()
            ];
        
        }

        if(isset($responce['return']) &&  isset($responce['return']['success']) && (int)$responce['return']['success']){
            $this->result = 1;
            $this->completed = 1;
            $this->completed_at = date("Y-m-d\TH:i:s",time());
            $this->params_out = json_encode($responce['return']);
        }else{
            $this->params_out = json_encode($responce['return']);
        }
        
        
        return $this->save();
    }

}