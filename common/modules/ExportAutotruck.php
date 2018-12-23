<?php

namespace common\modules;

use Yii;
use yii\db\Query;
use common\models\Request;
use common\models\Autotruck;
use common\dictionaries\AutotruckState;
use soapclient\methods\CreateReceipts;

class ExportAutotruck{


	public static function export(Autotruck $model){


		$params = [
			'guid'=>$model['guid'],
			'invoice'=>$model['invoice'],
			'date'=>$model['gtdDate'],
			'supplier_name'=>$model['decor'],
			'course'=>$model['course'],
			'warehouse'=>$model['statusTitle'],
			'transportnumber'=>$model['auto_number'],
			'country'=>$model['countryName'],
		];

		$params['table'] = (new Query())->select([
									'client.guid as client_guid',
									'app.count_place as count',
									'type_packaging.title as unit',
									'app.rate as priceforclient',
									'app.summa_us as amountforcustomer',
									'app.weight',
									'app.info as nomenclature_name'
								])
							->from("app")
							->leftJoin("client"," app.client = client.id")
							->leftJoin("type_packaging"," type_packaging.id = app.package")
							->where(['app.isDeleted'=>0,'autotruck_id'=>$model['id']])
							->all();

		try {
			$method = new CreateReceipts();
			$request = new Request([
                'request'=>get_class($method),
                'params_in'=>json_encode($params),
                'autotruck_id'=>$model['id'],
                'actor_id'=>Yii::$app->user->id
            ]);


            $method->setParameters($params);

            if(!$request->validate()){
                Yii::warning("Request validate error","ExportAutotruck");
                Yii::warning($request->getErrors(),"ExportAutotruck");
                return false; 
            }

            Yii::$app->db->createCommand()->update(Request::tableName(),['completed'=>1,'completed_at'=>date("Y-m-d\TH:i:s",time())],"`autotruck_id`=:autotruck_id AND `request`=:request AND  completed=0")
                ->bindValue(":request",$request->request)
                ->bindValue(":autotruck_id",$model['id'])
                ->execute();

            if($request->send($method)){
                $responce = json_decode($request->params_out,1);

                if(isset($responce['error'])){
                	Yii::warning("Error","ExportAutotruck");
                	Yii::warning($responce['error'],"ExportAutotruck");
                	Yii::$app->session->setFlash("warning","Ошибка при попытке выгрузить заявку в 1С");
                	Yii::$app->session->setFlash("error",$responce['error']);
                }

                if($request->result && isset($responce['guid']) && $responce['guid'] && isset($responce['number']) && $responce['number']){

                    $model->guid = $responce['guid'];
                    $model->doc_number = $responce['number'];

                    $model->state = AutotruckState::EXPORTED;
               		
                	Yii::$app->session->setFlash("info","Заявка выгружена в 1С");
                    return $model->save(1);
                }
            }
            
        } catch (\Exception $e) {
            Yii::warning($e->getMessage(),'api');
        }
        
        return false;
	}
}