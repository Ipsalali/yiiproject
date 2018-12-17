<?php
namespace pusher\wrapper;

/**
 * A simple yii logger.
 *
 * This simple logger implements the Log Interface and is the default logger for
 * all ApnsPHP_Abstract based class.
 *
 * This simple logger outputs The Message to standard output prefixed with date,
 * service name (ApplePushNotificationService) and Process ID (PID).
 *
 * @ingroup ApnsPHP_Log
 */
class Yii_ApnsPHP_Log implements \ApnsPHP_Log_Interface
{
	/**
	 * Logs a message.
	 *
	 * @param  $sMessage @type string The message.
	 */
	public function log($sMessage)
	{
		\Yii::info(date('r'). ' ApnsPHP['.getmypid().']: ' . trim($sMessage) . ' \n','yii_apns_log');
	}
}