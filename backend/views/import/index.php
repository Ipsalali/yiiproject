<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;

$this->title = "Импорт";
?>
<div class="row">
	<div class="col-xs-12">
		<ul class="nav nav-tabs">
  			<li class="active"><a data-toggle="tab" href="#excel" aria-expanded="true">Заявки</a></li>
			<li class=""><a data-toggle="tab" href="#googlesheet" aria-expanded="false">Google таблицы</a></li>
  		</ul>
  		<div class="tab-content">
  			<div id="excel" class="tab-pane fade active in">
  				<div class="row">
  					<div class="col-xs-12">
  						<h3>Заявки из Excel</h3>
  					</div>
  				</div>
  			</div>


  			<div id="googlesheet" class="tab-pane fade in">
  				<div class="row">
  					<div class="col-xs-12">
  						<h3>Google таблицы</h3>
  					</div>
  				</div>
  			</div>
  		</div>
	</div>
</div>