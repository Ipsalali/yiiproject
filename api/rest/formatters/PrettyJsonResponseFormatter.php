<?php
namespace api\rest\formatters;


use yii\helpers\Json;
use yii\web\JsonResponseFormatter;

class PrettyJsonResponseFormatter extends JsonResponseFormatter
{
    
    public $prettyPrint = false;


    public $encodeOptions = JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE;
    /**
     * Formats response data in JSON format.
     * @param Response $response
     */
    protected function formatJson($response)
    {
        $response->getHeaders()->set('Content-Type', 'application/json; charset=UTF-8');
        if ($response->data !== null) {
            $response->content = $this->prettyPrint 
            						? Json::encode($response->data, JSON_PRETTY_PRINT | $this->encodeOptions) 
            						: Json::encode($response->data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }
    }
}
?>