<?php
use yii\helpers\Html;
use common\helper\ArrayHelper;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use common\models\{SupplierCountry,Autotruck};
use common\models\Status;
use common\models\Sender;
use common\models\AutotruckImport;

ini_set("memory_limit", "512M");

$countries = SupplierCountry::find()->all();
$countriesIndexed = ArrayHelper::map($countries,'id','country');

$statuses = Status::find()->all();
$statusesIndexed = ArrayHelper::map($statuses,'id','title');

$senders = Sender::find()->all();
$sendersIndexed = ArrayHelper::map($senders,'id','name');

$sendersNamed = array_map(function($m){return mb_strtolower($m);},$sendersIndexed);

$importedAutotrucks=isset($autotruckImport->id)?Autotruck::find()->where(['imported'=>1,'import_source'=>$autotruckImport->id])->all():[];

$namesAutotrucks = count($importedAutotrucks) ? ArrayHelper::map($importedAutotrucks,'name','id') : [];

$this->title = "Импорт ".$autotruckImport->name;
$this->params['breadcrumbs'][] = ['link'=>['import/index'],'label'=>'Импорт'];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="card">
	<div class="card-header card-header-primary">
		<h2><?php echo $this->title;?></h2>
	</div>
	<div class="card-body">
                <?php if(!$autotruckImport->fileBinary){ ?>
                    <h3>Файл отсутствует</h3>
                  
                  <?php }else{
                    $parsedData = $autotruckImport->FileConvertArray();
                    if(is_array($parsedData)){
                      $sheetNames = array_keys($parsedData);
                  ?>
                  <h3>Листы:</h3>
                  <div class="row">
                    <div class="col-12" style="margin-top: 25px;">
                      <div class="card-header card-header-primary">
                        <div class="nav-tabs-navigation">
                          <ul class="nav nav-tabs" role='tablist'>
                          <?php foreach ($sheetNames as $i => $sheetName) { ?>
                              <li class="nav-item">
                                <a class='nav-link <?php echo $i == 0 ? "active" : "";?>' data-toggle="tab" href="#sheet_<?php echo $i;?>"><?php echo Html::encode($sheetName)?></a>
                              </li>
                          <?php } ?>
                          </ul>
                        </div>
                      </div>
                      <div class="tab-content">
                        <?php $i = 0; foreach ($parsedData as $sheetName => $sheet) { 
                            $titles = array_shift($sheet);
                        ?>
                          <div id="sheet_<?php echo $i;?>" class="tab-pane <?php echo $i == 0 ? "active" : "";?>">
                            <?php 
                             if(array_key_exists($sheetName, $namesAutotrucks)){
                            ?>
                              <div class="row">
                                <div class="col-5">
                                  <h3><?php echo "'".$sheetName."' импортирован!"?></h3>
                                  <?php echo Html::a("Посмотреть",null,['class'=>'btn btn-success']);?>
                                </div>
                              </div>
                            <?php
                            }else{
                             $form = ActiveForm::begin(['id'=>"fromAutotruck_$i",'action'=>['import/save-autotruck']]);

                             $model = new Autotruck();
                             $model->name = $sheetName;
                            ?>
                            
                            <div class="row">
                              <div class="col">
                                <h3><?php echo Html::encode($sheetName);?></h3>
                              </div>
                              <div class="col text-right">
                                <?php echo Html::submitButton("Сохранить",['class'=>'btn btn-success','style'=>'margin-top:20px;']);?>
                                <?php echo Html::hiddenInput('Autotruck[import_source]',$autotruckImport->id);?>
                              </div>
                            </div>

                            <div class="row">
                              <div class="col-12">
                                <div class="row">
                                    <div class="col-2">
                                      <?php echo $form->field($model,'date')->input("date",["class"=>"form-control"]);?>
                                    </div>
                                    <div class="col-3">
                                      <?php echo $form->field($model,'name')->input("text",['class'=>'form-control']);?>
                                    </div>
                                    <div class="col-1">
                                      <?php echo $form->field($model,'course')->input("number",['class'=>'form-control compute_sum compute_course']); ?>
                                    </div>
                                    <div  class="col-2">
                                      <?php echo $form->field($model,'country')->dropDownList($countriesIndexed,['prompt'=>'Выберите страну','class'=>'form-control']);?>
                                    </div>
                                </div>
                                <div class="row">
                                  <div class="col-5">
                                    <div class="row">
                                      <div  class="col-5">
                                        <?php echo $form->field($model,'status')->dropDownList($statusesIndexed,['prompt'=>'Выберите статус','class'=>'form-control']);?>
                                      </div>
                                      <!-- <div  class="col-xs-6">
                                        <label class="label-control">Дата изменения статуса</label>
                                        <?php //echo Html::input('date','Autotruck[status_date]',date("Y-m-d"),['class'=>'form-control']);?>
                                      </div> -->
                                    </div>
                                  </div>
                                  <div class="col-7">
                                    <?php echo $form->field($model,'description')->textarea(['class'=>'form-control']);?>
                                  </div>
                                </div>
                              </div>
                            </div>
                            <div class="row">
                              <div class="col-12">
                                <table class="table table-condensed">
                                  <thead>
                                    <tr>
                                      <?php //foreach ($titles as $title) { ?>
                                        <!-- <th scope="col"><?php //echo Html::encode($title);?></th> -->
                                      <?php //} ?>
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
                                    <?php foreach ($sheet as $row_i => $row) {
                                        
                                        $info = isset($row['груз']) ? Html::encode($row['груз']) : null;
                                        $sender = isset($row['отправитель']) ? Html::encode(mb_strtolower($row['отправитель'])) : null;
                                        $count_place = isset($row['кол-во мест']) ? (int)$row['кол-во мест'] : null;
                                        $weight = isset($row['вес (кг)']) ? Html::encode($row['вес (кг)']) : null;

                                        $join = [];
                                        isset($row['получатель']) ? array_push($join,Html::encode($row['получатель'])) : null;
                                        isset($row['маркировка']) ? array_push($join,Html::encode($row['маркировка'])) : null;
                                        
                                        $comment = count($join) ? implode(" - ", $join) : null;

                                        if(!$info  && !$sender && !$count_place && !$weight&& !$comment){
                                          continue;
                                        }
                                     ?>
                                      <tr class="row_app" data-number="<?php echo $row_i?>">
                                        <td>
                                          <?php echo ++$row_i;?>
                                          <?php echo Html::hiddenInput("App[$row_i][imported]",1);?>
                                        </td>
                                        <td><?php echo Html::textInput("App[$row_i][info]",$info,['class'=>'form-control']);?></td>
                                        <td>
                                            <?php

                                                $likes = $sender ? ArrayHelper::like('~'.$sender.'~i', $sendersNamed) : [];
                                                $v = reset($likes);
                                                $k = $v ? array_search($v, $sendersNamed) : false;
                                                $options = $k !== false ? [$k=>$sendersNamed[$k]] : $sendersNamed;
                                                echo Html::dropDownList("App[$row_i][sender]",$k,$options,['class'=>'form-control','prompt'=>'Выберите отправителя']);
                                            ?>
                                        </td>
                                        <td><?php echo Html::textInput("App[$row_i][count_place]",$count_place,['class'=>'form-control']);?></td>
                                        <td><?php echo Html::textInput("App[$row_i][weight]",$weight,['class'=>'form-control']);?></td>
                                        <td><?php echo Html::textInput("App[$row_i][comment]",$comment,['class'=>'form-control']);?></td>
                                        <td>
                                          <?php echo Html::a("x",null,['class'=>'btn btn-sm btn-danger btn-remove-row']);?>
                                        </td>
                                      </tr>
                                    <?php }?>
                                  </tbody>
                                </table>
                              </div>
                            </div>
                            <?php ActiveForm::end();?>
                            <?php }?>
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