<?php

namespace WSUserActions\widgets\websocket;

use Yii;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\web\View;

class WebSocket extends \yii\bootstrap\Widget{

	public $host = "ws://127.0.0.1:8000";

	public $table_name;
	
	public $record_id;
	
	public $user_id;
	
	public $event;

	public $register_url;
	
	public $resetUrl;
	
	public $redirectLocation;
	

	public function init(){
        parent::init();
        
        $view = Yii::$app->getView();
        $view->registerJs($this->getJs());
    }


    public function run(){
    	return "";
    }

    private function getJs(){
        
		$resetUrl = $this->resetUrl;
		$t = $this->table_name;
		$record_id = $this->record_id;
		$user_id = $this->user_id;
		$redirectLocation = $this->redirectLocation;
		$event = $this->event;
		$host = $this->host;
		
    	return <<<JS

		//--------------- WebSocket Widget ------------------------------
		var params = {
				table_name:"$t",
				record_id:$record_id,
				user_id:$user_id,
				event:"$event"
		};
		
		var resetStateActions = function(){
			$.ajax({
				url:'$resetUrl',
				dataType:'json',
				success:function(resp){
					console.log("resetStateActions:");
					console.log(resp);
				},
				error:function(msg){
					console.log(msg);
				}
			});
		}
		var packet = JSON.stringify(params);

		socket = new WebSocket("$host/"
									+"?table_name="+params.table_name
									+"&record_id="+params.record_id
									+"&user_id="+params.user_id
									+"&event="+params.event
								);
        

        socket.onopen = function() {
		  	console.log("Соединение установлено.");
		  	//socket.send(packet);
		};

		socket.onclose = function(event) {
		  	if (event.wasClean) {
		    	console.log('Соединение закрыто чисто');
		  	} else {
		    	console.log('Обрыв соединения'); // например, "убит" процесс сервера
		  	}
		  	resetStateActions();
		  	console.log('Код: ' + event.code + ' причина: ' + event.reason);
		};

        socket.onmessage = function(evt) {
        	console.log("Получены данные: "+evt.data);
        	var msg = JSON.parse(evt.data);
        	console.log(msg);
        	if(msg.hasOwnProperty("error") && msg.hasOwnProperty("errorCode")){
        		if(msg.error == "busyResource" && parseInt(msg.errorCode) == 403){
        			location.href = '$redirectLocation';
        		}
        	}
        };

        socket.onerror = function(error) {
		  	console.log("Ошибка " + error.message);
		};
		//--------------- WebSocket Widget ------------------------------
JS;
    }

}