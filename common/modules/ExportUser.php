<?php 

namespace common\modules;

use Yii;
use common\models\User;
use common\models\Request;
use soapclient\methods\LoadCustomer;


class ExportUser{

	public static function export(User $user){
		if(!isset($user->id)) return false;

		try {
            $data =[
                'guid'=>'',
                'name'=>$user->name,
                'email'=>$user->email
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