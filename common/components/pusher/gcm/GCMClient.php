<?php

/**
 * @author: Albaraa Mishlawi
 * @package: albaraam/php-gcm
 */

namespace pusher\gcm;

use pusher\gcm\exceptions\AuthenticationException;
use pusher\gcm\exceptions\HttpException;
use pusher\gcm\exceptions\IlegalApiKeyException;
use pusher\gcm\exceptions\TooBigPayloadException;
use pusher\gcm\exceptions\TooManyRecipientsException;
use pusher\gcm\exceptions\NoRecipientsException;

class GCMClient {

	/**
	 * @var string
	 */

	protected $url = 'https://android.googleapis.com/gcm/send';
	
	/**
	 * @var string
	 */
	protected $apiKey = false;

	
	public function __construct($apiKey)
	{
		$this->apiKey = $apiKey;
	}
	
	public function send(GCMMessage $message)
	{
		if(!$message->isValid()){
			return $message->getErrors();
		}

		if (!$this->apiKey) {
			throw new IlegalApiKeyException("Api Key is empty");
		}

		if (count($message->getTo()) <= 0)
		{
			throw new NoRecipientsException("Add at least 1 recipient (GCM Registration ID)");
		}

		if (count($message->getTo()) > 1000)
		{
			throw new TooManyRecipientsException("Recipients maximum is 1000 GCM Registration IDs");
		}

		$data = $this->generateJSONMessage($message);

		if(!empty($data))
		{
			if (strlen($data) > 4096)
				throw new TooBigPayloadException("Data payload maximum is 4096 bytes");
		}

		$headers = array(
			'Content-Type: application/json',
			'Authorization: key='.$this->apiKey,
		);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->url);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

		$resultBody = curl_exec($ch);
		$resultHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

		curl_close($ch);

		switch ($resultHttpCode)
		{
			case "200":
				break; // its ok
			case "400":
			case "401":
				throw new AuthenticationException("HTTP Authentication Error", $resultHttpCode);
			default:
				throw new HttpException("HTTP Error", $resultHttpCode);
		}

		$response =  new GCMResponse($message, $resultBody);

		return $response;
	}
	
	protected function generateJSONMessage(GCMMessage $message)
	{
		$data = array(
			'notification' => $message->getNotification()->toArray(),
			'collapse_key' => $message->getCollapseKey(),
			'delay_while_idle' => $message->getDelayWhileIdle(),
			'time_to_live' => $message->getTimeToLive(),
			'restricted_package_name' => $message->getRestrictedPackageName(),
			'dry_run' => $message->getDryRun(),
		);

		if($message->getData() != null){
			$data['data'] = $message->getData();
		}

		if(is_array($message->getTo())){
			$data['registration_ids'] = $message->getTo();
		}else{
			$data['to'] = $message->getTo();
		}
		return json_encode($data);
	}
}