<?php


use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;
use yii\grid\GridView;
use yii\data\ActiveDataProvider;
use kartik\date\DatePicker;
use kartik\daterange\DateRangePicker;
use yii\helpers\Url;
use frontend\models\PaymentsExpenses;
use common\models\Organisation;
use common\widgets\autocomplete\AutoComplete;

use frontend\modules\PaymentsExpensesReport;
$this->title = "Отчет по организациям";

$managers = $PaymentsExpensesReport->getManagers();

$managersAutocompleteByFullName = array_map(function($a){
	$fn = $a['full_name'];
	return ['value'=>$fn,'label'=>$fn];
}, $managers);

?>

<div class="row">
	<div class="col-xs-12">
		<h3>Отчет по организациям</h3>
	</div>
</div>

	<?php 

	$layout = <<< HTML
    {input1}<br>
    {input2}
    <span class="input-group-addon kv-date-remove">
        <i class="glyphicon glyphicon-remove"></i>
    </span>
HTML;

	echo GridView::widget([
			'id' => 'gridSessions',
			'dataProvider'=>$dataProvider,
			'filterModel'=>$PaymentsExpensesReport,
			'formatter' => ['class' => 'yii\i18n\Formatter','nullDisplay' => ''],
			'tableOptions' => [
            	'id'=>'sessions','class'=>'table table-striped table-bordered'
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
					'filter'=>DatePicker::widget([
							'model'=>$PaymentsExpensesReport,
							'language'=>'ru',
							'attribute'=>'date_from',
							'attribute2'=>'date_to',
							'options' => ['placeholder' => 'c'],
							'options2' => ['placeholder' => 'по'],
							'type' => DatePicker::TYPE_RANGE,
							'separator'=>" по ",
							'layout'=>$layout,
							'pluginOptions'=>[
								'autoclose'=>true,
								
        						'format' => 'dd.mm.yyyy'
    						]
						])
				],
			
			[
				'attribute'=>'organisation',
				'format'=>'raw',
				'value'=>function($m){
					return $m->organisation ? $m->org->org_name : "Не указан";
				},
				'filter'=>Html::activeDropDownList($PaymentsExpensesReport,'organisation',ArrayHelper::map(Organisation::find()->all(),'id','org_name'),['class'=>'form-control','prompt'=>'Выберите организацию'])
			],
			//[
			//	'attribute'=>'sum',
			//	'label'=>'Сумма $',
			//	'format'=>'raw',
			//	'value'=>function($m){
			//		
			//		$sum = null;
			//		if($m->sum_cash > 0 || $m->sum_card > 0){
			//			
			//			if($m->sum > 0 && (int)$m->toreport == 1){
			//				$sum = $m->sum;
			//			}
            //
			//		}else{
			//			$sum = $m->sum;	
			//		}
            //
			//		//$sum = $m->sum_cash > 0 || $m->sum_card > 0 ? null : $m->sum;
			//		PaymentsExpensesReport::$common_sum += $sum;
			//		return $sum;
			//		//return $m->org->payment == Organisation::PAY_CASH ? $m->sum : $m->sum_card ;
			//	},
			//	
			//	'footerOptions'=>['class'=>'ft_sum','id'=>'total_sum']
			//],
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
			//[
			//	'attribute'=>'sum_cash',
			//	'label'=>'Наличные(руб)',
			//	'format'=>'raw',
			//	'value'=>function($m){
			//		
			//		$sum_cash = null;

			//		if($m->sum_cash > 0 && ((int)$m->toreport == 0 || (int)$m->toreport == 2)){
			//			$sum_cash = $m->sum_cash;
			//		}
			//		
			//		PaymentsExpensesReport::$common_sum_cash += $sum_cash;
			//		
//
//					return $sum_cash;
//				
//				},
//				
//				'footerOptions'=>['class'=>'ft_sum_cash','id'=>'total_sum_cash']
//			],
			[
				'attribute'=>'toreport',
				'label'=>'Отчет по',
				'format'=>'raw',
				'value'=>function($m){
					
					//$sum_card = null;

					//if($m->sum_card > 0 && ((int)$m->toreport == 0 || (int)$m->toreport == 3)){
					//	$sum_card = $m->sum_card;
					//}
					
					//PaymentsExpensesReport::$common_sum_card += $sum_card;
					//return $sum_card;
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

			//	$("#total_sum_card").text(<?php echo PaymentsExpensesReport::$common_sum_card;?>);
			$("#total_sum").text(<?php echo PaymentsExpensesReport::$common_sum;?>);
			//	$("#total_sum_cash").text(<?php echo PaymentsExpensesReport::$common_sum_cash;?>);

			});
		</script>
	</div>
