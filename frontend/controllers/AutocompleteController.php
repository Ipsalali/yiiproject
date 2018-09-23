<?php

namespace frontend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\web\HttpException;

class AutocompleteController extends Controller{


	/**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index', 'index'],
                        'allow' => true,
                        'roles' => ['autotruck/create','autotruck/update'],
                    ],
                ],
            ]
        ];
    }

	public function actionClients(){

		$post = new Post;

		$data = $post->find()->all();

		return $this->render('index',array('data'=>$data));
	}



}