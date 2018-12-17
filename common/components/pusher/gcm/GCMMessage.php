<?php
/**
 * @author: Albaraa Mishlawi
 * @package: albaraam/php-gcm
 */

namespace pusher\gcm;


class GCMMessage
{

    /**
     * Recipients - List of GCM registration IDs (from 1 to 1000 recipients allowed)
     * @var array
     */
    private $to = array();

	/**
	 * An arbitrary string (such as "Updates Available") that is used to collapse a group of like messages
	 * when the device is offline, so that only the last message gets sent to the client.
	 * This is intended to avoid sending too many messages to the phone when it comes back online.
	 * Note that since there is no guarantee of the order in which messages get sent, the "last" message
	 * may not actually be the last message sent by the application server.
	 * Collapse keys are also called send-to-sync messages.
	 *
	 * Optional.
	 *
	 * @var string|null
	 */
	private $collapseKey = null;

	/**
	 * Key-value pairs od messages payload data.
	 * Max payload size is 4kB
	 *
	 * Optional.
	 *
	 * @var array|null
	 */
	private $data = null;

	/**
	 * Key-value pairs od messages payload data.
	 * Max payload size is 4kB
	 *
	 * Optional.
	 *
	 * @var Notification|null
	 */
	private $notification = null;

	/**
	 * If included, indicates that the message should not be sent immediately if the device is idle.
	 * The server will wait for the device to become active, and then only the last message
	 * for each collapse_key value will be sent.
	 *
	 * Optional.
	 *
	 * @var boolean
	 */
	private $delayWhileIdle = false;

	/**
	 * How long (in seconds) the message should be kept on GCM storage if the device is offline.
	 *
	 * Optional (default time-to-live is 4 weeks).
	 *
	 * @var int
	 */
	private $timeToLive = null;

	/**
	 * A string containing the package name of your application.
	 * When set, messages will only be sent to registration IDs that match the package name.
	 *
	 * Optional.
	 *
	 * @var string|null
	 */
	private $restrictedPackageName = null;

	/**
	 * If included, allows developers to test their request without actually sending a message.
	 *
	 * Optional.
	 *
	 * @var boolean
	 */
	private $dryRun = false;

	/**
	 * Validation errors
	 *
	 * @var array
	 */

	private $_errors = [];


	public function __construct($notification = null, $toRegId)
	{
        $this->setTo($toRegId);
        if($notification != null){
            $this->setNotification($notification);
        }
		
	}


	public function getCollapseKey()
	{
		return $this->collapseKey;
	}


	public function setCollapseKey($collapseKey)
	{
		$this->collapseKey = $collapseKey;
		return $this;
	}


	public function getData()
	{
		return $this->data;
	}


	public function setData($data)
	{
		$this->data = $data;
		return $this;
	}

	public function getNotification()
	{
		if($this->notification == null){
			$this->notification = new Notification();
		}
		return $this->notification;
	}


	public function setNotification($notification)
	{
		$this->notification = $notification;
		return $this;
	}

    public function getTo($onlyOne = false)
    {
        if($onlyOne)
            return current($this->to); // firstone
        return $this->to;
    }


    public function setTo($to)
    {
        $this->to = $to;
        //$this->addTo($to);

        return $this;
    }


    public function addTo($to)
    {
        if(!is_string($to))
            throw new WrongGcmIdException("Recipient must be string GCM Registration ID");

        if(!is_array($this->to)){
        	$this->to = [$this->to];
        }
        $this->to[] = $to;
        return $this;
    }


	public function getDelayWhileIdle()
	{
		return $this->delayWhileIdle;
	}


	public function setDelayWhileIdle($delayWhileIdle)
	{
		$this->delayWhileIdle = $delayWhileIdle;
		return $this;
	}


	public function getTimeToLive()
	{
		return $this->timeToLive;
	}


	public function setTimeToLive($timeToLive)
	{
		$this->timeToLive = $timeToLive;
		return $this;
	}


	public function getRestrictedPackageName()
	{
		return $this->restrictedPackageName;
	}


	public function setRestrictedPackageName($restrictedPackageName)
	{
		$this->restrictedPackageName = $restrictedPackageName;
		return $this;
	}


	public function getDryRun()
	{
		return $this->dryRun;
	}


	public function setDryRun($dryRun)
	{
		$this->dryRun = $dryRun;
		return $this;
	}

	public function isValid()
	{
		if($this->getNotification()->getTitle() == ""){
			$this->addError("Notification title is required");
		}
		return (count($this->_errors) > 0) ? false : true;
	}

	public function getErrors()
	{
		return $this->_errors;
	}

    public function addError($message)
    {
        $this->_errors[] = $message;
    }

	/**
     * Returns the value of an object property.
     *
     * Do not call this method directly as it is a PHP magic method that
     * will be implicitly called when executing `$value = $object->property;`.
     * @param string $name the property name
     * @return mixed the property value
     * @throws UnknownPropertyException if the property is not defined
     * @throws InvalidCallException if the property is write-only
     * @see __set()
     */
    public function __get($name)
    {
        $getter = 'get' . $name;
        if (method_exists($this, $getter)) {
            return $this->$getter();
        }/* elseif (method_exists($this, 'set' . $name)) {
            throw new InvalidCallException('Getting write-only property: ' . get_class($this) . '::' . $name);
        } else {
            throw new UnknownPropertyException('Getting unknown property: ' . get_class($this) . '::' . $name);
        }*/
    }

    /**
     * Sets value of an object property.
     *
     * Do not call this method directly as it is a PHP magic method that
     * will be implicitly called when executing `$object->property = $value;`.
     * @param string $name the property name or the event name
     * @param mixed $value the property value
     * @throws UnknownPropertyException if the property is not defined
     * @throws InvalidCallException if the property is read-only
     * @see __get()
     */
    public function __set($name, $value)
    {
        $setter = 'set' . $name;
        if (method_exists($this, $setter)) {
            $this->$setter($value);
        }/* elseif (method_exists($this, 'get' . $name)) {
            throw new InvalidCallException('Setting read-only property: ' . get_class($this) . '::' . $name);
        } else {
            throw new UnknownPropertyException('Setting unknown property: ' . get_class($this) . '::' . $name);
        }*/
    }

}