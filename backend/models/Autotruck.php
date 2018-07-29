<?php

namespace backend\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Expression;
use backend\models\App;
use common\models\Status;
use common\models\Client;
use backend\models\AppTrace;
use backend\models\AutotruckNotification;
use yii\db\Query;
use frontend\helpers\Mail;
use frontend\models\Autotruck as fAutotruck;
use common\models\SupplierCountry;
use frontend\models\ExpensesManager;
use frontend\helpers\Checkexcel;

/**
*
*
*
*/

class Autotruck extends fAutotruck
{


	

    

    public function afterDelete(){
        // AppTrace::deleteAll("autotruck_id=".$this->id);
        // App::deleteAll("autotruck_id=".$this->id);
        // AutotruckNotification::deleteAll("autotruck_id=".$this->id);
        // CustomerPayment::deleteAll("autotruck_id=".$this->id);
        // ExpensesManager::deleteAll("autotruck_id=".$this->id);
        
        return parent::afterDelete();
    }

  

    
}