<?php

use yii\helpers\Html;
use common\helper\ArrayHelper;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use common\models\{SupplierCountry,Autotruck};
use common\models\Status;
use common\models\Sender;
use common\models\AutotruckImport;


$this->title = "Импорт";
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="card">
  <div class="card-header card-header-primary">
    <h1 class="card-title"><?php echo $this->title?></h1>
    <div class="nav-tabs-navigation">
      <ul class="nav nav-tabs" role='tablist'>
        <li class="nav-item"><a class='nav-link active' data-toggle="tab" href="#excel" aria-expanded="true">Заявки</a></li>
        <li class="nav-item"><a class='nav-link' data-toggle="tab" href="#googlesheet" aria-expanded="false">Google таблицы</a></li>
      </ul>
    </div>
  </div>
	<div class="card-body">
		
  		<div class="tab-content">
  			<div id="excel" class="tab-pane active ">
  				<div class="row">
  					<div class="col-12">
  						<h3>Заявки из Excel</h3>
  					</div>
  				</div>
          <div class="row">
            <div class="col-4">
              <?php $form =  ActiveForm::begin(['id'=>'formAutotruckImport']);?>
              <?php echo $form->field($autotruckImport,'file')->fileInput();?>
              <?php echo Html::submitInput("Загрузить");?>
              <?php ActiveForm::end();?>
            </div>
            
            <div class="col-5">
              <div class="importStories">
                <h4>Ранее загруженные файлы для импорта:</h4>
                <ul>
                <?php
                  $imports = AutotruckImport::getAllWithOutFile();
                  foreach ($imports as $key => $import) {
                    ?>
                    <li><?php echo Html::a($import['name'],['import/open','id'=>$import['id']]);?></li>
                    <?php
                  }
                ?>
                </ul>
              </div>
            </div>
          </div>
  			</div>


  			<div id="googlesheet" class="tab-pane">
  				<div class="row">
  					<div class="col-12">
  						<h3>Google таблицы</h3>
  					</div>
  				</div>
  			</div>
  		</div>
	</div>
</div>

<?php 

$js = <<<JS
    $("body").on("click",".btn-remove-row",function(event){
      if(confirm("Подтвердите удаление!")){
        var row = $(this).parents("tr");
        if(row.length)
          row.remove();
      }
    });
JS;

$this->registerJs($js);
?>