<?php 

namespace common\modules;

use Yii;
use common\models\Client;
use common\models\Request;
use soapclient\methods\LoadCustomer;


class ExportClient{

	public static function export(Client $client){
		if(!isset($client->id)) return false;

		try {
            $data =[
                'guid'=>'',
                'name'=>$client->name,
                'email'=>$client->email
            ];
            $method = new LoadCustomer($data);

            $request = new Request([
                'request'=>get_class($method),
                'params_in'=>json_encode($method->attributes),
                'user_id'=>null,
                'actor_id'=>Yii::$app->user->id
            ]);

            if($request->save() && $request->send($method)){
                      
            }else{
                    
            }

        }catch(\Exception $e) {
            throw $e;
        }
	}

}


?>