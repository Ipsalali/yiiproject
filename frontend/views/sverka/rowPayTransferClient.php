<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use common\models\Currency;
use common\models\PaymentClientByTransfer;

$contractors = PaymentClientByTransfer::getContractors();
?>

<?php if(!isset($model->id)){?>
	<tr class="pay pay_form">
<?php } ?>
	<td>
		<?php echo isset($model->id) ? $n : "";?>
		<?php echo isset($model->id) ? Html::hiddenInput("pay[{$n}][id]",$model->id) : "";?>
	</td>
	<td><?php echo Html::input("date","pay[{$n}][date]",date("Y-m-d",isset($model->id) ? strtotime($model->date) : time()));?></td>
	<td><?php echo Html::dropDownList("pay[{$n}][currency]",$model->currency,ArrayHelper::map(Currency::getCurrencies(),"id","title"),['prompt'=>"Выберите валюту"]);?></td>
	<td><?php echo Html::input("number","pay[{$n}][course]",$model->course);?></td>
	<td><?php echo Html::input("number","pay[{$n}][sum]",$model->sum,['class'=>'req']);?></td>
	<td><?php echo Html::input("number","pay[{$n}][sum_ru]",$model->sum_ru,['class'=>'req']);?></td>
	<td><?php echo Html::dropDownList("pay[{$n}][contractor]",$model->contractor_org,$contractors,['prompt'=>"Выберите контрагента"]);?></td>
	<td><?php echo Html::input("text","pay[{$n}][comment]",$model->comment);?></td>
	<td>
		<?php 
			if(isset($model->id)){
				echo Html::a("X",null,['class'=>'btn btn-primary closePayClientForm']);
				echo Html::a("<i class=\"glyphicon glyphicon-trash\"></i>",['sverka/remove-client-pay-by-transfer'],['class'=>'btn btn-danger delete_pay_transfer_client','data-id'=>$model['id']]);
			}else{ ?>
				<a class="btn btn-danger remove_pay_transfer_client">X</a>
			<?php } ?>
		
		
	</td>
<?php if(!isset($model->id)){?>
	</tr>
<?php } ?>