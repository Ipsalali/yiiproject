<?php 

namespace common\modules;

use Yii;
use common\models\Client;
use common\models\Request;
use soapclient\methods\LoadCustomer;


class ExportClient{




	public static function export(Client $client){
		if(!isset($client->id)) return false;
        Yii::warning("Call method ExportClient::export","export1C");
        
        try {
            $data =[
                'guid'=>$client->guid,
                'name'=>$client->name,
                'email'=>$client->userEmail
            ];

            
            Yii::warning("Export client to 1C","export1C");
            $method = new LoadCustomer($data);

            $request = new Request([
                'request'=>get_class($method),
                'params_in'=>json_encode($method->attributes),
                'user_id'=>null,
                'actor_id'=>Yii::$app->user->id
            ]);


            Yii::warning($request->params_in,"export1C");
            if($method->validate() && $request->send($method)){
                $params = json_decode($request->params_out,1);
                Yii::warning("Response params: ","export1C");
                Yii::warning($request->params_out,"export1C");
                if(isset($params['success']) && boolval($params['success']) && isset($params['guid'])){
                    $client->guid = $params['guid'];

                    Yii::warning($request->params_out,"export1C");
                    if($client->save(1)){
                        Yii::warning("client exported successfully and saved guid","export1C");
                        return true;
                    }else{
                        Yii::warning("client exported successfully but not saved guid","export1C");
                    }

                }else{
                    Yii::warning("Invalid response parameters","export1C");
                }
            }else{
                Yii::warning("Method LoadCustomer parameters has errors: ","export1C");
                Yii::warning(json_encode($method),"export1C");
            }

        }catch(\Exception $e) {
            Yii::warning("Method LoadCustomer parameters has errors: ","export1C");
            Yii::warning($e->getMessage(),"export1C");
        }

        return false;
	}

}


?>