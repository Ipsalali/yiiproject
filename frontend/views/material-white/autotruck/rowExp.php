<?php
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use common\models\Currency;

$class = "ExpensesManager[{$n}]";
?>

<tr class="exp_row">
	<td>-</td>
	<td>
		<?php echo Html::input("date",$class."[date]",null,['class'=>'form-control']);?>
	</td>
	<td>
		<?php echo Html::dropDownList($class."[manager_id]",null,ArrayHelper::map($expManagers,'id','name'),['prompt'=>'Выберите менеджера','class'=>'form-control manager_id']);?>
	</td>
	<td>
		<?php echo Html::textInput($class."[cost]",null,['class'=>'cost form-control']);?>
	</td>
	<td>
		<?php echo Html::textInput($class."[comment]",null,['class'=>'exp_comment form-control']);?>
	</td>
	<td>
		<?php echo Html::button("<i class=\"material-icons\">close</i>",["rel"=>"tooltip",'class'=>'btn btn-danger btn-sm btn-round remove_exp','data-confirm'=>'Подтвердите свои дейсвтия']);?>
	</td>
</tr>