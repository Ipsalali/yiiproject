<?php

namespace frontend\bootstrap4;


class AlertJS extends \frontend\bootstrap4\BaseWidget
{
    /**
     * @var array the alert types configuration for the flash messages.
     * This array is setup as $key => $value, where:
     * - $key is the name of the session flash variable
     * - $value is the bootstrap alert type (i.e. danger, success, info, warning)
     */
    public $alertTypes = [
        'error'   => 'alert-danger',
        'danger'  => 'alert-danger',
        'success' => 'alert-success',
        'info'    => 'alert-info',
        'warning' => 'alert-warning',
        'default' => '',
        'rose' => '',
        'primary' => ''
    ];

    /**
     * @var array the options for rendering the close button tag.
     */
    public $closeButton = [];

    public function init()
    {
        parent::init();
        
        $session = \Yii::$app->session;
        $flashes = $session->getAllFlashes();
        
        $view = $this->getView();
        $view->registerJs($this->getNotifyScript());

        foreach ($flashes as $type => $data) {
            if (isset($this->alertTypes[$type])) {
                $data = (array) $data;
                $scripts = [];
                foreach ($data as $i => $message) {
                    $script = <<<JS
                        showAlertJsNotification("{$message}","{$type}");
JS;
                    array_push($scripts, $script);
                }

                if(count($scripts)){
                    $this->getView()->registerJs(implode("\n", $scripts));
                }
                $session->removeFlash($type);
            }
        }
    }

    public function getNotifyScript(){
        return <<<JS
            var showAlertJsNotification =  function(message,kind) {
                var type = {
                    default:'', 
                    info:'info', 
                    error:'danger', 
                    success:'success', 
                    warning:'warning', 
                    rose:'rose', 
                    primary:'primary'
                };

                var color = typeof type[kind] != 'undefined' ? type[kind] : "success";
                
                $.notify({
                  icon: "add_alert",
                  message: message
                }, {
                  type: color,
                  timer: 2000,
                  placement:{
                    from:"bottom",
                    align:"right"
                  }
                });
            }
JS;
    }

}
