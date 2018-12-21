<?php 

namespace common\modules;

use Yii;
use common\models\User;
use common\models\Request;
use soapclient\methods\LoadCustomer;


class ExportUser{

	public static function export(User $user){
		if(!isset($user->id)) return false;
        Yii::warning("Call method ExportUser::export","export1C");
		try {
            $data =[
                'guid'=>'',
                'name'=>$user->name,
                'email'=>$user->email
            ];
            Yii::warning("Export user to 1C","export1C");
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
                    $user->guid = $params['guid'];

                    Yii::warning($request->params_out,"export1C");
                    if($user->save(1)){
                        Yii::warning("User exported successfully and saved guid","export1C");
                        return true;
                    }else{
                        Yii::warning("User exported successfully but not saved guid","export1C");
                    }

                }else{
                    Yii::warning("Invalid response parameters","export1C");
                }
            }else{
                Yii::warning("Method LoadCustomer parameters has errors: ","export1C");
                Yii::warning(json_encode($method->getErrors()),"export1C");
            }

        }catch(\Exception $e) {
            Yii::warning("Method LoadCustomer throw Exception: ","export1C");
            Yii::warning($e->getMessage(),"export1C");
        }

        return false;
	}

}


?>