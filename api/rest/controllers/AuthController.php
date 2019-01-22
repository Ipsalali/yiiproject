<?php

namespace api\rest\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\rest\ActiveController;
use yii\rest\Controller;

use api\rest\models\AuthForm;

class AuthController extends Controller
{
    
	protected function verbs(){
        return [
            'index' => ['post'],
        ];
    }

	public function actionIndex(){

		$model = new AuthForm();
        $model->load(Yii::$app->request->bodyParams, '');
        if ($token = $model->auth()) {
            return $token;
        } else {
            return $model;
        }
	}

}