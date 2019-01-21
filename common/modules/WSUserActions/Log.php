<?php
namespace WSUserActions;


class Log{


  	public static function w($message){
 		$f = fopen("socket.log", "a+");
      	fwrite($f, "\n\n[".date("Y-m-d H:i:s",time())."]: ");
      	fwrite($f, $message."\n");
      	fclose($f);     
  	}

}