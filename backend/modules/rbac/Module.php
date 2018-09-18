<?php

namespace backend\modules\rbac;

use Yii;

class Module extends \yii\base\Module
{

    public $modelNamespace = '';
    public $componentNamespace = '';

    public $customViews = [];


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
        if (!isset(Yii::$app->i18n->translations['rbac']) && !isset(Yii::$app->i18n->translations['rbac/*'])) {
            Yii::$app->i18n->translations['rbac'] = [
                'class' => 'yii\i18n\PhpMessageSource',
                'basePath' => '@backend/modules/rbac/messages',
                'forceTranslation' => true,
                'fileMap' => [
                    'rbac' => 'rbac.php'
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
}
