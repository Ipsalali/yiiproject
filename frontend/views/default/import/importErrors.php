<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use moonland\phpexcel\Excel;
use common\models\SupplierCountry;
use common\models\Status;
use common\models\Sender;

$countries = SupplierCountry::find()->all();
$countriesIndexed = ArrayHelper::map($countries,'id','country');

$statuses = Status::find()->all();
$statusesIndexed = ArrayHelper::map($statuses,'id','title');

$senders = Sender::find()->all();
$sendersIndexed = ArrayHelper::map($senders,'id','name');

$this->title = "Ошибка импорта '".$autotruck['name']."'";
?>
<div class="row">
  <div class="col-xs-5">
    <h1><?php echo $this->title;?></h1>
  </div>
</div>
<div class="row">
	<div class="col-xs-12">
		<?php if(isset($autotruck->id)){ ?>
            <div class="row">
              	<div class="col-xs-12">
                 	<?php $form = ActiveForm::begin(['id'=>"fromAutotruck",'action'=>['import/save-autotruck']]);?>
                        <div class="row">
                            <div class="col-xs-4">
                                
                            </div>
                            <div class="col-xs-4 col-xs-offset-4 text-right">
                                <?php echo Html::submitButton("Сохранить",['class'=>'btn btn-success','style'=>'margin-top:20px;']);?>
                                <?php echo Html::hiddenInput('Autotruck[id]',$autotruck->id);?>
                                <?php echo Html::hiddenInput('Autotruck[import_source]',$autotruck->import_source);?>
                                <?php echo Html::hiddenInput('Autotruck[imported]',1);?>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xs-12">
                                <div class="row">
                                    <div class="col-xs-2">
                                      <?php echo $form->field($autotruck,'date')->input("date",["class"=>"form-control"]);?>
                                    </div>
                                    <div class="col-xs-3">
                                      <?php echo $form->field($autotruck,'name')->input("text",['class'=>'form-control']);?>
                                    </div>
                                    <div class="col-xs-1">
                                    <?php echo $form->field($autotruck,'course')->input("number",['class'=>'form-control compute_sum compute_course']); ?>
                                    </div>
                                    <div  class="col-xs-2">
                                      <?php echo $form->field($autotruck,'country')->dropDownList($countriesIndexed,['prompt'=>'Выберите страну','class'=>'form-control']);?>
                                    </div>
                                </div>
                                <div class="row">
                                  <div class="col-xs-5">
                                    <div class="row">
                                      <div  class="col-xs-5">
                                        <?php echo $form->field($autotruck,'status')->dropDownList($statusesIndexed,['prompt'=>'Выберите статус','class'=>'form-control']);?>
                                      </div>
                                    </div>
                                  </div>
                                  <div class="col-xs-7">
                                    <?php echo $form->field($autotruck,'description')->textarea(['class'=>'form-control']);?>

                                  </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12">
                             	<table class="table table-condensed">
                                  	<thead>
                                    	<tr>
	                                      <td>#</td>
	                                      <td>Наименование</td>
	                                      <td>Отправитель</td>
	                                      <td>Количество мест</td>
	                                      <td>Вес</td>
	                                      <td>Комментарии</td>
	                                      <td></td>
	                                    </tr>
	                                </thead>
	                                <tbody>
                                    <?php foreach ($errorApps as $row_i => $app) {
                                    	$errors = is_object($app) ? $app->getErrors() : array();
                                    ?>
                                      <tr class="row_app" data-number="<?php echo $row_i?>">
                                        <td>
                                          <?php echo ++$row_i;?>
                                          <?php echo isset($app['id']) ? Html::hiddenInput('App[$row_i][id]',$app['id']) : "";?>
                                        </td>
                                        <td>
                                        	<?php 
                                        		$e = array_key_exists('info',$errors) ? $errors['info'] : null;
                                        		echo Html::textInput("App[$row_i][info]",$app['info'],['class'=>'form-control']);
                                        	?>
                                        	<span style="color: #f00;"><?php echo is_array($e) && count($e) ? $e[0] : null;?></span>
                                        </td>
                                        <td><?php echo Html::dropDownList("App[$row_i][sender]",$app['sender'],$sendersIndexed,['class'=>'form-control','prompt'=>'Выберите отправителя']);?></td>
                                        <td>
                                        	<?php 
                                        		$e = array_key_exists('count_place',$errors) ? $errors['count_place'] : null;
                                        		echo Html::textInput("App[$row_i][count_place]",$app['count_place'],['class'=>'form-control']);
                                        	?>
                                        	<span style="color: #f00;"><?php echo is_array($e) && count($e) ? $e[0] : null;?></span>
                                        </td>
                                        <td>
                                        	<?php
                                        		$e = array_key_exists('weight',$errors) ? $errors['weight'] : null;
                                        		echo Html::textInput("App[$row_i][weight]",$app['weight'],['class'=>'form-control']);
                                        	?>
                                        	<span style="color: #f00;"><?php echo is_array($e) && count($e) ? $e[0] : null;?></span>
                                        </td>
                                        <td>
                                        	<?php 
                                        		$e = array_key_exists('comment',$errors) ? $errors['comment'] : null;
                                        		echo Html::textInput("App[$row_i][comment]",$app['comment'],['class'=>'form-control']);
                                        	?>
                                        	<span style="color: #f00;"><?php echo is_array($e) && count($e) ? $e[0] : null;?></span>
                                        </td>
                                        <td>
                                          <?php echo !isset($app['id']) ? Html::a("x",null,['class'=>'btn btn-sm btn-danger btn-remove-row']) : "";?>
                                        </td>
                                      </tr>
                                    <?php }?>
                                  </tbody>
                                </table>
                            </div>
                        </div>
                    <?php ActiveForm::end();?>
              	</div>
            </div>
        <?php } ?>
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