<?php

namespace backend\modules\profiler;

use Yii;

class Profiler extends \yii\base\Module
{

	public $modelNamespace = '';

    public $customViews = [];

    public $customMailViews = [];

    public $mailViewPath = '';

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
        self::registerTranslations();
    }



    public static function registerTranslations()
    {
        if (!isset(Yii::$app->i18n->translations['profiler']) && !isset(Yii::$app->i18n->translations['profiler/*'])) {
            Yii::$app->i18n->translations['profiler'] = [
                'class' => 'yii\i18n\PhpMessageSource',
                'basePath' => '@backend/modules/profiler/messages',
                'forceTranslation' => true,
                'fileMap' => [
                    'profiler' => 'profiler.php'
                ]
            ];
        }
    }



    public function getCustomView($default)
    {
        if (isset($this->customViews[$default])) {
            return $this->customViews[$default];
        } else {
            return $default;
        }
    }

    public function getCustomMailView($default)
    {
        if (isset($this->customMailViews[$default])) {
            return $this->customMailViews[$default];
        } else {
            return $this->mailViewPath . $default;
        }
    }
}
