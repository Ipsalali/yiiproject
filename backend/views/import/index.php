<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use moonland\phpexcel\Excel;
use common\models\SupplierCountry;
use common\models\Status;

$countries = SupplierCountry::find()->all();
$countriesIndexed = ArrayHelper::map($countries,'id','country');

$statuses = Status::find()->all();
$statusesIndexed = ArrayHelper::map($statuses,'id','title');


$senders = Sender::find()->all();
$sendersIndexed = ArrayHelper::map($senders,'id','name');

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
                    $parsedData = $autotruckImport->FileConvertArray();
                    if(is_array($parsedData)){
                      $sheetNames = array_keys($parsedData);
                  ?>
                  <div class="row">
                    <div class="col-xs-12">
                      <ul class="nav nav-tabs">
                      <?php foreach ($sheetNames as $i => $sheetName) { ?>
                          <li class="<?php echo $i == 0 ? "active" : "";?>">
                            <a data-toggle="tab" href="#sheet_<?php echo $i;?>"><?php echo Html::encode($sheetName)?></a>
                          </li>
                      <?php } ?>
                      </ul>
                      <div class="tab-content">
                        <?php $i = 0; foreach ($parsedData as $sheetName => $sheet) { 
                            $titles = array_shift($sheet);
                        ?>
                          <div id="sheet_<?php echo $i;?>" class="tab-pane fade in <?php echo $i == 0 ? "active" : "";?>">
                            <?php $form = ActiveForm::begin(['id'=>"fromAutotruck_$i",'action'=>['import/save-autotruck']]);?>
                            <div class="row">
                              <div class="col-xs-4">
                                <h3><?php echo Html::encode($sheetName);?></h3>
                              </div>
                              <div class="col-xs-4 col-xs-offset-4 text-right">
                                <?php echo Html::submitButton("Сохранить",['class'=>'btn btn-success','style'=>'margin-top:20px;']);?>
                              </div>
                            </div>

                            <div class="row">
                              <div class="col-xs-12">
                                <div class="row">
                                    <div class="col-xs-2">
                                      <label class="label-control">Дата</label>
                                      <?php echo Html::input("date",'Autotruck[date]',date("Y-m-d"),["class"=>"form-control"]);?>
                                    </div>
                                    <div class="col-xs-3">
                                      <label class="label-control">Инвойс</label>
                                      <?php echo Html::input("text",'Autotruck[name]',$sheetName,['class'=>'form-control']);?>
                                    </div>
                                    <div class="col-xs-1">
                                      <label class="label-control">Курс</label>
                                    <?php echo Html::input("number",'Autotruck[course]',0,['class'=>'form-control compute_sum compute_course']); ?>
                                    </div>
                                    <div  class="col-xs-2">
                                      <label class="label-control">Страна</label>
                                      <?php echo Html::dropDownList('Autotruck[country]',null,$countriesIndexed,['prompt'=>'Выберите страну','class'=>'form-control']);?>
                                    </div>
                                </div>
                                <div class="row">
                                  <div class="col-xs-5">
                                    <div class="row">
                                      <div  class="col-xs-5">
                                        <label class="label-control">Статус</label>
                                        <?php echo Html::dropDownList('Autotruck[status]',null,$statusesIndexed,['prompt'=>'Выберите статус','class'=>'form-control']);?>
                                      </div>
                                      <div  class="col-xs-6">
                                        <label class="label-control">Дата изменения статуса</label>
                                        <?php echo Html::input('date','Autotruck[status_date]',date("Y-m-d"),['class'=>'form-control']);?>
                                      </div>
                                    </div>
                                  </div>
                                  <div class="col-xs-7">
                                    <label class="label-control">Комментарии</label>
                                    <?php echo Html::textarea('Autotruck[description]',null,['class'=>'form-control']);?>
                                  </div>
                                </div>
                              </div>
                            </div>
                            <div class="row">
                              <div class="col-xs-12">
                                <table class="table table-condensed">
                                  <thead>
                                    <tr>
                                      <?php foreach ($titles as $title) { ?>
                                        <th scope="col"><?php echo Html::encode($title);?></th>
                                      <?php } ?>
                                    </tr>
                                  </thead>
                                  <tbody>
                                    <?php foreach ($sheet as $row_i => $row) { ?>
                                      <tr>
                                        <?php foreach ($row as $title => $value) { ?>
                                          <td><?php echo Html::encode($value);?></td>
                                        <?php }?>
                                      </tr>
                                    <?php }?>
                                  </tbody>
                                </table>
                              </div>
                            </div>
                            <?php ActiveForm::end();?>
                          </div>
                        <?php $i++; } ?>
                      </div>
                    </div>
                  </div>
                <?php }
                  } 
                ?>
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