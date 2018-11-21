<?php

namespace console\controllers;

class ImportController extends \yii\console\controllers\MigrateController{

	public $migrationTable = '{{%import}}';

	public $migrationPath = ['@app/import'];
}