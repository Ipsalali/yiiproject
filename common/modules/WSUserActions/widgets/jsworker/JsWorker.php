<?php

namespace WSUserActions\widgets\jsworker;

use Yii;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\web\View;

class JsWorker extends \yii\bootstrap\Widget{

    public $jsPath = "@WSUserActions/assets/js";

    public $tableName;

    public $resourse_id = 0;

    public $user_id = 0;

    public $event;

    public $registerUrl;

    public $redirectLocation;

    public $resetUrl;

    public $_csrf;

	public function init(){
        parent::init();
        
        $this->_csrf = Yii::$app->request->getCsrfToken();
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
        $jsW = new JsWorkerAsset(['sourcePath'=>$this->jsPath]);
        $jsW::register($view);
    }

    private function getJs(){

    	return <<<JS

		//--------------- JsWorker Widget ------------------------------

        function clientController(userparams){
            this.params = jQuery.extend({
                tableName:null,
                resourse_id:null,
                user_id:null,
                event:"update",
                registerUrl:"",
                redirectLocation:"",
                resetUrl:"",
                _csrf:""
            },userparams);


            this.startClient = function(){};
            this.stopClient = function(){};
        };


		if(!window.Worker){ 
            worker = null;
            console.log('Browser don`t support the Worker');
            throw new Error();
        }

        if(worker){ throw new Error();  }
                
        var indicator = document.createElement("div");
                
        document.body.appendChild(indicator);
        var s = document.createElement("span");
        s.innerText = "*";
        indicator.appendChild(s);

        var indicate = function(){
            indicator.style.opacity = parseInt(indicator.style.opacity) == 1 ? 0 : 1;
        }

        var worker = new Worker("{$this->jsPath}/worker.js");
                

        worker.onmessage = function (event){
            indicate();
            var data = event.data;
            if(typeof data == "object"){

            }
            console.log("Сообщение из потока воркера в основной");
            console.log(event.data);
        };

        var clController = new clientController({
            tableName:"{$this->tableName}",
            resourse_id:{$this->resourse_id},
            user_id:{$this->user_id},
            event:"{$this->event}",
            registerUrl:"{$this->registerUrl}",
            redirectLocation:"{$this->redirectLocation}",
            resetUrl:"{$this->resetUrl}",
            _csrf:"{$this->_csrf}",
        });        

        clController.startClient = function(){
            if(!worker){return;}    
            
            worker.postMessage({action:"run",params:this.params});
        }


        clController.stopClient = function(params){
            if(!worker){ return;}

            worker.postMessage({action:"stop",params:{}});
        }

        clController.startClient({});
		//--------------- JsWorker Widget ------------------------------
JS;
    }

}