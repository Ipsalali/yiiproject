<?php

namespace WSUserActions;

use Workerman\Worker;
use WSUserActions\Log;
use WSUserActions\models\UserAction;

class WSWorker extends \yii\base\Module{


	public $modelNamespace = '';

    public $websocket_host = "websocket://0.0.0.0";

    public $websocket_port = 8000;

    public $websocket_localhost = 'ws://127.0.0.1';

    public $tcp_host = 'tcp://127.0.0.1';

    public $tcp_port = 1234;

    private $worker = null;

    public $pullConnections = [];

    /**
     * Constructor.
     * @param string $id the ID of this module.
     * @param Module $parent the parent module (if any).
     * @param array $config name-value pairs that will be used to initialize the object properties.
     */
    public function __construct($id, $parent = null, $config = [])
    {
        $this->id = $id;
        $this->module = $parent;

        $config_local = require(__DIR__ . '/config/config.php');
        $config = array_merge($config_local,$config);
        
        parent::__construct($id,$parent,$config);
    }


    public function init()
    {	
        parent::init();
    }


    public function build(){
    	// create a ws-server. all your users will connect to it
        $this->worker = new Worker($this->websocket_host.":".$this->websocket_port);

        $this->pullConnections = [];
        $pullConnections = &$this->pullConnections;

		    $this->worker->onConnect = function($connection) use (&$pullConnections){
            $connection->onWebSocketConnect = function($connection) use (&$pullConnections){
                // put get-parameter into $users collection when a new user is connected
                // you can set any parameter on site page. for example client.html: ws = new WebSocket("ws://127.0.0.1:8000/?user=tester01");

                $get = $_GET;

                if($action = UserAction::isBusyAction($get)){
                 	$msg = new Message([
                 		'success'=>0,
                 		'error'=>Message::ERROR_RESOURCE_BUSY,
                 		'errorCode'=>Message::ERROR_RESOURCE_BUSY_CODE,
                 		'action'=>$action->getAttributes()
                 	]);
                  	
                  	$connection->send(json_encode($msg->attributes));
                  	return null;
                }

                $action = new UserAction();
                $action = $action->register($get);

                if($action->id){
                  	$this->pullConnections[$action->id]['action'] = $action;
                  	$this->pullConnections[$action->id]['connection'] = $connection;

                  	$msg = new Message([
                 		'success'=>1,
                 		'status'=>Message::STATUS_NEW_CONNECTION,
                 		'action'=>$action->getAttributes()
                 	]);

	                foreach ($pullConnections as $action_id => $con) {
	                    $con['connection']->send(json_encode($msg->attributes));
	                }
                  
                }elseif($action->hasErrors()){
                    Log::w(json_encode($action->getErrors()));
                    $msg = new Message([
                 		'success'=>0,
                 		'error'=>Message::ERROR_RESOURCE_BUSY,
                 		'errorCode'=>Message::ERROR_RESOURCE_BUSY_CODE,
                 		'action'=>$action->getAttributes()
                 	]);
                  	$connection->send(json_encode($msg->attributes));
                }
                // or you can use another parameter for user identification, for example $_COOKIE['PHPSESSID']
            };
        };

        $this->worker->onClose = function($connection) use(&$pullConnections)
        {
            // unset parameter when user is disconnected
            $cs = [];
            foreach ($pullConnections as $key => $ac) {
              $cs[$key]=$ac['connection'];
            }
            $id = array_search($connection, $cs);

            if(!isset($pullConnections[$id])) return null;
            $action = $pullConnections[$id];
            
            if($action['action'] instanceof UserAction){
              $action = $action['action']->getAttributes();
            
              // $row = UserAction::close($action);
              $row = UserAction::deleteAction($action);

              if($row){
                unset($pullConnections[$id]);
                foreach ($pullConnections as $c_id => $connection) {
                  $connection['connection']->send("{$c_id}: Connection {$id} closed");
                }
                Log::w("{$id}: Connection {$id} closed");
              }else{
                Log::w("{$id}: Connection {$id} didn`t closed");
              }
            }
        };

        // Emitted when data is received
        $this->worker->onMessage = function($connection, $data) use (&$pullConnections){
            // you have to use for $data json_decode because send.php uses json_encode
            
        };

        $tcp_host = $this->tcp_host;
        $tcp_port = $this->tcp_port;
        

        // it starts once when you start server.php:
        $this->worker->onWorkerStart = function() use (&$pullConnections,$tcp_host,$tcp_port)
        {
            // create a local tcp-server. it will receive messages from your site code (for example from send.php)
            $inner_tcp_worker = new Worker($tcp_host.":".$tcp_port);
            
            // create a handler that will be called when a local tcp-socket receives a message (for example from send.php)
            $inner_tcp_worker->onMessage = function($connection, $data) use (&$pullConnections) {
                
            };

            $inner_tcp_worker->listen();
        };


    }



    public function runAll(){
    	// Run worker
    	Worker::runAll();
    }

}