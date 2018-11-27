<?php

namespace common\helper;

class EmailChecker{


	public static function validName($email){
		$emailV = new \yii\validators\EmailValidator();

        return $emailV->validate($email);
	}



	public static function checkMethod1($email){
		
		if(!self::validName($email)) return false;

		$re='/.*@(.*)/';
		preg_match($re, $email, $match);
		$domain=$match[1];

		if (@getmxrr($domain, $MXHost)) 
			return true;
		else {
		    $f = @fsockopen($domain, 25, $errno, $errstr, 30);
		    
		    if($f){
		        fclose($f);
		        return 0;
		    }
		    else 
		    	return false;
		}
	}


  	public static function checkMethod($email = ""){
	    $timeout = 10;

	    if(!self::validName($email)) return false;

		$re='/.*@(.*)/';
		preg_match($re, $email, $match);
		$host=$match[1];

		$host .= ".";

	    if (getmxrr ($host, $mxhosts[0], $mxhosts[1]) == true)  
	    	array_multisort ($mxhosts[1], $mxhosts[0]);
	    else { 
	    	$mxhosts[0] = $host;
	       	$mxhosts[1] = 10;
	    }

	    

	    $port = 25;
	    $localhost = $_SERVER['HTTP_HOST'];
	    $sender = 'info@tcrm-web-ali.ru';// . $localhost;

	    $result = false;
	    $id = 0;
	    while(!$result && $id < count ($mxhosts[0])){ 
	    	if(function_exists ("fsockopen")){ 
	    		
	           	if ($connection = fsockopen ($mxhosts[0][$id], $port, $errno, $error, $timeout)){
	              	fputs ($connection,"HELO $localhost\r\n"); // 250
	              	$data = fgets ($connection,1024);
	              	$response = substr ($data,0,1);
	              	if($response == '2'){ // 200, 250 etc.
		                fputs ($connection,"MAIL FROM:<$sender>\r\n");
		                $data = fgets($connection,1024);

		                $response = substr ($data,0,1);
	                	if($response == '2'){ // 200, 250 etc.
	               
	                  		fputs ($connection,"RCPT TO:<$email>\r\n");
	                  		$data = fgets($connection,1024);
	                  		$response = substr ($data,0,1);
	              			
	              			if($response == '2'){ // 200, 250 etc.
	                 
	                    		fputs($connection,"data\r\n");
	                   			$data = fgets($connection,1024);
	                    		$response = substr($data,0,1);
	                    
	                    		if($response == '2'){ 
	                    			$result = true; 
	                    		}
	                     	}
	              	 	}
	             	}
	          		
	          		fputs ($connection,"QUIT\r\n");
	              	fclose ($connection);
	              	if ($result) return true;
	            }
	       	}
	      	else 
	      	 	break;
	      	
	      	$id++;
	    } //while

	    return false;
	}


	public static function checkMethod3($email){

		if(!self::validName($email)) return false;

		$re='/.*@(.*)/';
		preg_match($re, $email, $match);
		$domain=$match[1];

		$mx_records = dns_get_record($domain,DNS_MX);

		$port = 25; //Порт работы сервера
		if (($socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP)) < 0) {
		    //AF_INET - семейство протоколов
		    //SOCK_STREAM - тип сокета
		    //SOL_TCP - протокол
		    echo "\nSocket creating error\n";
		}

		foreach ($mx_records as $key => $mx) {
			print_r($mx['target']);
			// $result = socket_connect($socket, $mx['target'], $port);
			// if ($result === false) {
			//     echo "\nError connecting to socket\n";
			// } else {
			//    echo "\nSocket connected\n";
			// }
		}
		
		print_r($mx_records);
		exit;
	}



}