<?php

namespace backend\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\backend\Query;
use common\models\App as cApp;


/**
*
*
*
*/

class App extends cApp
{

	public function rules(){
		return [
            [['info'], 'required']
        ];
	}

}