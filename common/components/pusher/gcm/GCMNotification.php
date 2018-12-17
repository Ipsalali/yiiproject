<?php 
/**
 * @author: Albaraa Mishlawi
 * @package: albaraam/php-gcm
 */

namespace pusher\gcm;


/**
* GCM Payload Notification
*/
class GCMNotification
{
    /*
     * @var string (required) Indicates notification title.
     * */
    private $title;


    /*
     * @var string (optional) Indicates notification body text.
     * */
    private $body;

    /*
     * @var string (required) Indicates notification icon. sets value to myicon for drawable resource myicon.
     * */
    private $icon;

    /*
     * @var string (optional) Indicates sound to be played
     * */
    private $sound;

    /*
     * @var string (optional)
     *
     * Indicates whether each notification message results in a new entry on the notification center on Android.
     * If not set, each request creates a new notification. If set, and a notification with the same tag is already
     *  being shown, the new notification replaces the existing one in notification center.
     *
     * */
    private $tag;

    /*
     * @var string (optional) Indicates color of the icon, expressed in #rrggbb format
     *
     * */
    private $color;

    /*
     * @var string (optional)
     *
     * The action associated with a user click on the notification.
     * On Android, if this is set, an activity with a matching intent filter is launched when user clicks the notification. For example, if one of your Activities includes the intent filter:
     * <intent-filter>
     *      <action android:name="OPEN_ACTIVITY_1" />
     *      <category android:name="android.intent.category.DEFAULT" />
     * </intent-filter>
     * Set click_action to OPEN_ACTIVITY_1 to open it.
     * If set, corresponds to category in APNS payload.
     *
     * */
    private $click_action;

    /*
     * @var string (optional) Indicates the key to the body string for localization.
     *
     * On Android, use the key in the app's string resources when populating this value.
     *
     * */
    private $body_loc_key;

    /*
     * @var JSON array as string (optional) Indicates the string value to replace format specifiers in body string
     *  for localization.
     *
     * On Android, these are the format arguments for the string resource. For more information, see:
     * http://developer.android.com/guide/topics/resources/string-resource.html#FormattingAndStyling
     *
     * */
    private $body_loc_args;

    /*
     * @var string (optional) Indicates the key to the title string for localization.
     *
     * On Android, use the key in the app's string resources when populating this value.
     *
     * */
    private $title_loc_key;

    /*
     * @var JSON array as string (optional) Indicates the string value to replace format specifiers in title string
     *  for localization.
     *
     * On Android, these are the format arguments for the string resource. For more information, see:
     * http://developer.android.com/guide/topics/resources/string-resource.html#FormattingAndStyling
     *
     * */
    private $title_loc_args;
    
    private $content_available;



    function __construct($title = "", $body = "") {
        $this->title = $title;
        $this->body = $body;
    }


    /******************************************** Getters & Setters *************************************************/

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param mixed $body
     */
    public function setBody($body)
    {
        $this->body = $body;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * @param mixed $icon
     */
    public function setIcon($icon)
    {
        $this->icon = $icon;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSound()
    {
        return $this->sound;
    }

    /**
     * @param mixed $sound
     */
    public function setSound($sound)
    {
        $this->sound = $sound;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * @param mixed $tag
     */
    public function setTag($tag)
    {
        $this->tag = $tag;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * @param mixed $color
     */
    public function setColor($color)
    {
        $this->color = $color;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getClickAction()
    {
        return $this->click_action;
    }

    /**
     * @param mixed $click_action
     */
    public function setClickAction($click_action)
    {
        $this->click_action = $click_action;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getBodyLocKey()
    {
        return $this->body_loc_key;
    }

    /**
     * @param mixed $body_loc_key
     */
    public function setBodyLocKey($body_loc_key)
    {
        $this->body_loc_key = $body_loc_key;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getBodyLocArgs()
    {
        return $this->body_loc_args;
    }

    /**
     * @param mixed $body_loc_args
     */
    public function setBodyLocArgs($body_loc_args)
    {
        $this->body_loc_args = $body_loc_args;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTitleLocKey()
    {
        return $this->title_loc_key;
    }

    /**
     * @param mixed $title_loc_key
     */
    public function setTitleLocKey($title_loc_key)
    {
        $this->title_loc_key = $title_loc_key;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTitleLocArgs()
    {
        return $this->title_loc_args;
    }

    /**
     * @param mixed $title_loc_args
     */
    public function setTitleLocArgs($title_loc_args)
    {
        $this->title_loc_args = $title_loc_args;
        return $this;
    }
    
    /**
     * @return mixed
     */
    public function getContentAvailable()
    {
        return $this->content_available;
    }

    /**
     * @param mixed $content_available
     */
    public function setContentAvailable($content_available)
    {
        $this->content_available = $content_available;
        return $this;
    }

    public function toArray()
    {
        return[
            "title" => $this->title,
            "body" => $this->body,
            "icon" => $this->icon,
            "sound" => $this->sound,
            "tag" => $this->tag,
            "color" => $this->color,
            "click_action" => $this->click_action,
            "body_loc_key" => $this->body_loc_key,
            "body_loc_args" => $this->body_loc_args,
            "title_loc_key" => $this->title_loc_key,
            "title_loc_args" => $this->title_loc_args,
            "content_available" => $this->content_available,
        ];
    }
}
?>
