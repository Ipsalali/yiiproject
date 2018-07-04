<?php

namespace frontend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use common\models\Post;
use yii\web\HttpException;

class PostController extends Controller{

	public $layout = "/post/main";

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
                        'roles' => ['Post/index'],
                    ],
                    [
                        'actions' => ['create', 'index'],
                        'allow' => true,
                        'roles' => ['Post/create'],
                    ],
                    [
                        'actions' => ['read', 'index'],
                        'allow' => true,
                        'roles' => ['Post/read'],
                    ],
                    [
                        'actions' => ['update', 'index'],
                        'allow' => true,
                        'roles' => ['Post/update'],
                    ],
                    [
                        'actions' => ['delete', 'index'],
                        'allow' => true,
                        'roles' => ['Post/delete'],
                    ]
                ],
            ]
        ];
    }

	public function actionIndex(){

		$post = new Post;

		$data = $post->find()->all();

		return $this->render('index',array('data'=>$data));
	}

	public function actionCreate(){
		$model = new Post;

		if(isset($_POST['Post'])){
			$model->title = $_POST['Post']['title'];
			$model->content = $_POST['Post']['content'];

			if($model->save()){
				Yii::$app->response->redirect(array("post/read",'id'=>$model->id));
			} 
		}

		return $this->render('create',array('model'=>$model));
	}

	public function actionRead($id = NULL){

		if($id == null)
			throw new HttpException(404,'Not Found!');

		$post = Post::findOne($id);

		if($post === NULL)
			throw new HttpException(404,'Document Does Not Exist');

		return $this->render('read',array("post"=>$post));

	}

	public function actionUpdate($id = null){
		if($id == null)
			throw new HttpException(404, 'Not Found');

		$post = Post::findOne($id);

		if ($post === NULL)
        	throw new HttpException(404, 'Document Does Not Exist');
		
		if (isset($_POST['Post']))
    	{
        	$post->title = $_POST['Post']['title'];
        	$post->content = $_POST['Post']['content'];
 
        	if ($post->save())
            	Yii::$app->response->redirect(array('post/read', 'id' => $post->id));
    	}

    	return $this->render('create', array(
        	'model' => $post
    	));

	}

	public function actionDelete($id = NULL){

		if($id == NULL){
			Yii::$app->session->setFlash("PostDeleteError");
			return Yii::$app->response->redirect(array("post/index"));
		}

		$post = Post::findOne($id);

		if($post === NULL){
			Yii::$app->session->setFlash("PostDeleteError");
			return Yii::$app->response->redirect(array("post/index"));
		}

		$post->delete();

		Yii::$app->session->setFlash("PostDeleted");
		return Yii::$app->response->redirect(array("post/index"));
	}

}