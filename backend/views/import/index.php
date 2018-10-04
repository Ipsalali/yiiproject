<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use moonland\phpexcel\Excel;



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
          <div class="row">
            <div class="col-xs-4">
              <?php $form =  ActiveForm::begin(['id'=>'formAutotruckImport']);?>
              <?php echo $form->field($autotruckImport,'file')->fileInput();?>
              <?php echo Html::submitInput("Загрузить");?>
              <?php ActiveForm::end();?>
            </div>
          </div>
          <?php if(isset($autotruckImport->id)){ ?>
            <div class="row">
              <div class="col-xs-12">
                <?php if(!$autotruckImport->fileBinary){ ?>
                    <h3>Файл отсутствует</h3>
                  
                  <?php }else{ 

                    $autotruckImport->FileConvertArray();
                  ?>

                <?php } ?>
              </div>
            </div>
          <?php } ?>
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