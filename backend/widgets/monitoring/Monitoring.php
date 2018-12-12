<?php

namespace backend\widgets\monitoring;

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\web\View;
use backend\widgets\monitoring\MonitoringAsset;
use Yii;

class Monitoring extends \yii\bootstrap\Widget{

    protected $default_data = [
                "title"=>"Мониторинг",
                'showTitle'=>false,
                'pageUrl'=>"",

                "urlUpdate"=>"",
                
                "gridView"=>"",
    ];

    public $options = array();

    public function init(){
        parent::init();
        
        $view = Yii::$app->getView();
        
        $this->registerAssets();
        $view->registerJs($this->getJs());
    }



    public function run(){

        $data = array_merge($this->default_data,$this->options);
        
        $html = $this->view->render($this->viewPath."/monitoring.php",['data'=>$data]);
        
        return $html;

    }


    /**
    * Register the needed assets
    */
    public function registerAssets(){
        $view = $this->getView();
        MonitoringAsset::register($view);
    }



    public function getViewPath(){
        return "@backend/widgets/monitoring/views";
    }



    private function getJs(){
        
    return <<<JS


JS;
    }

}

?>