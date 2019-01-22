<?php

namespace WSUserActions\widgets\jsworker;

use Yii;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\web\View;

class JsWorker extends \yii\bootstrap\Widget{

	public function init(){
        parent::init();
        
        $this->registerAssets();
        $view = Yii::$app->getView();
        $view->registerJs($this->getJs());
    }


    public function run(){
    	return "";
    }


    /**
    * Register the needed assets
    */
    public function registerAssets(){
        $view = $this->getView();
        JsWorkerAsset::register($view);
    }

    private function getJs(){

    	return <<<JS

		//--------------- JsWorker Widget ------------------------------
		
		//--------------- JsWorker Widget ------------------------------
JS;
    }

}