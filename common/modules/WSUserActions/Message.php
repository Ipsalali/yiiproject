<?php
namespace WSUserActions;

use yii\base\Model;

class Message extends Model{


  	const ERROR_RESOURCE_BUSY = "busyResource";
  	

  	const ERROR_RESOURCE_BUSY_CODE = 403;

  	const STATUS_NEW_CONNECTION = "newAction";

  	public $success = 0;


  	public $error;


  	public $errorCode;


  	public $status;


  	public $action;

}