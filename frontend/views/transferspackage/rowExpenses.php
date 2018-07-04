<?php
use yii\helpers\Html;
use yii\helpers\ArrayHelper;

$class = "SellerExpenses[{$n}]";
?>

<tr>
	<td>-</td>
	<td>
		<?php echo Html::input("date",$class."[date]",null,['class'=>'form-control']);?>
	</td>
	<td>
		<?php echo Html::dropDownList($class."[seller_id]",null,ArrayHelper::map($sellers,'id','name'),['prompt'=>'Выберите поставщика','class'=>'form-control']);?>
	</td>
	<td>
		<?php echo Html::textInput($class."[sum]",null,['class'=>'sum form-control']);?>
	</td>
	<td>
		<?php echo Html::textInput($class."[comment]",null,['class'=>'form-control']);?>
	</td>
	<td>
		<?php echo Html::a("X",null,['class'=>'btn btn-danger removeRow','data-confirm'=>'Подтвердите свои дейсвтия']);?>
	</td>
</tr>