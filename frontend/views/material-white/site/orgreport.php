<?php


use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;
use frontend\bootstrap4\GridView;
use yii\data\ActiveDataProvider;

use frontend\models\PaymentsExpenses;
use common\models\Organisation;
use common\widgets\autocomplete\AutoComplete;
use frontend\modules\PaymentsExpensesReport;

$this->title = "Отчет по организациям";
$this->params['breadcrumbs'][]=$this->title;

$managers = $PaymentsExpensesReport->getManagers();
$managersAutocompleteByFullName = array_map(function($a){
	$fn = $a['full_name'];
	return ['value'=>$fn,'label'=>$fn];
}, $managers);


$organisations = Organisation::find()->all();
$organisationsIndexed = ArrayHelper::map($organisations,'id','org_name');
?>
<div class="card">
	<div class="card-header card-header-primary">
		<h3 class="card-title">Отчет по организациям</h3>
	</div>
	<div class="card-body">
		
	<?php 
		echo GridView::widget([
			'id' => 'gridSessions',
			'dataProvider'=>$dataProvider,
			'filterModel'=>$PaymentsExpensesReport,
			'formatter' => ['class' => 'yii\i18n\Formatter','nullDisplay' => ''],
			'tableOptions' => [
            	'id'=>'sessions','class'=>'table table-sm table-striped table-bordered'
        	],
        	'summary'=>'',
        	'rowOptions'=>function($model){
        		
    		},
    		'showFooter'=>true,
			'columns'=>[
				[
					'attribute'=>'id',
					'label'=>'Номер',
					'format'=>'raw',
					'value'=>function ($a) {

                        return "<span class='payment_info'>#".$a->id."</span>";
                    },
                    'footer'=>'Итого'
				],
				[
					'attribute'=>'date',
					'contentOptions'=>['class'=>'date_td'],
					'value'=>function ($a) {
                            return date("d.m.Y",strtotime($a->date));
                        },
					'format'=>'raw',
					'filter'=>Html::input("date",'PaymentsExpensesReport[date_from]',$PaymentsExpensesReport->date_from ? date("Y-m-d",strtotime($PaymentsExpensesReport->date_from)) : null,['class'=>'form-control']) . Html::input("date",'PaymentsExpensesReport[date_to]',$PaymentsExpensesReport->date_to ? date("Y-m-d",strtotime($PaymentsExpensesReport->date_to)) : null,['class'=>'form-control']),
				],
			[
				'attribute'=>'organisation',
				'format'=>'raw',
				'value'=>function($m) use($organisationsIndexed){
					return $m->organisation && array_key_exists($m->organisation, $organisationsIndexed)? $organisationsIndexed[$m->organisation] : "Не указан";
				},
				'filter'=>Html::activeDropDownList($PaymentsExpensesReport,'organisation',$organisationsIndexed,['class'=>'form-control','prompt'=>'Выберите организацию'])
			],
			[
    			'attribute'=>'sum_search',
    			'label'=>'Сумма',
    			'format'=>'raw',
    			'value'=>function($m){
    			 	
    			 	$sum = 0;
    			 	
					
					if((int)$m['toreport'] == 1){
					    $sum = $m->sum;
					}elseif((int)$m['toreport'] == 2){
					    $sum = $m->sum_cash;
					}elseif((int)$m['toreport'] == 3){
					    $sum = $m->sum_card;
					}
					
					//Если расход, вычисляем,а если оплата прибавляем
					
					if(PaymentsExpensesReport::$select_toreport > 0){
					   if((int)$m['plus'] == 1){
    					   PaymentsExpensesReport::$common_sum += $sum; 
    					}else{
    					   PaymentsExpensesReport::$common_sum -= $sum;
    					} 
					}else{
					    PaymentsExpensesReport::$common_sum = null;
					}
					
					
					
    			 	return $sum;
    			},
    			'footerOptions'=>['class'=>'ft_sum','id'=>'total_sum']
			],
			[
				'attribute'=>'toreport',
				'label'=>'Отчет по',
				'format'=>'raw',
				'value'=>function($m){
					$t = "";
					if((int)$m['toreport'] == 1){
					    $t = "Сумма ($)";
					}elseif((int)$m['toreport'] == 2){
					    $t = "Сумма (руб)";
					}elseif((int)$m['toreport'] == 3){
					    $t = "Сумма Б/Н(руб)";
					}
					
					return $t;
				    
				},
				'filter'=>Html::activeDropDownList($PaymentsExpensesReport,'toreport',['1'=>'Сумма ($)','2'=>'Сумма (руб)','3'=>"Сумма Б/Н(руб)"],['prompt'=>"Все",'class'=>'form-control'])
			],
			[
				'attribute'=>'payment',
				'label'=>'Способ оплаты',
				'format'=>'raw',
				'value'=>function($m){

					return $m->paymentLabel;
				},
				'filter'=>Html::activeDropDownList($PaymentsExpensesReport,'payment',array_merge(["none"=>'Выберите способ оплаты'],Organisation::$pay_labels),['class'=>'form-control'])
			],

			[
				'attribute'=>'plus',
				'label'=>'Вид',
				'format'=>'raw',
				'value'=>function($m){
					return $m->plus ? "Поступление" : "Расход";
				},
				'filter'=>Html::activeDropDownList($PaymentsExpensesReport,'plus',['1'=>'Поступление','0'=>'Расход'],['prompt'=>'Все','class'=>'form-control'])
			],

			[
				'attribute'=>'manager_fullname',
				'label'=>'Контрагент',
				'format'=>'raw',
				'value'=>function($m){
					$mg = $m->manager_id;
					if($mg){
						$cl = $m->manager->client;
						if($cl && isset($cl->id)){
							return $cl->full_name;
						}else{
							return $m->manager->name;
						}
					}else{
						return "Не указан";
					}
				},
				'filter'=>AutoComplete::widget([
				    'model' => $PaymentsExpensesReport,
    				'attribute' => 'manager_fullname',
					'clientOptions' => [
					    'source' => $managersAutocompleteByFullName,
						'autoFill'=>true,
						'minLength'=>2
					],
					'includeJs'=>false,
					'options' => [
			             'class' => 'form-control'
			        ]
				])
			],
			
			],
		])?>
		
		<script type="text/javascript">
			$(function(){
				$("#total_sum").text(<?php echo PaymentsExpensesReport::$common_sum;?>);
			});
		</script>
	</div>
</div>