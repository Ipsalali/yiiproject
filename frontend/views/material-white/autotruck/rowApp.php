<?php
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use common\models\Currency;

$class = "App[{$n}]";
$type_class = (!$type)? "type_app":"type_service";
?>

<tr class="app_row <?php echo $type_class;?>">
	<td>
		-
		<?php echo Html::hiddenInput($class."[type]",$type)?>
	</td>

	<!-- Клиент -->
	<td>
		<?php echo Html::dropDownList($class."[client]",null,ArrayHelper::map($clients,'id','name'),['prompt'=>'Выберите клиента','class'=>'form-control app_client']);?>
	</td>

	<!-- Отправитель -->
	<?php if(!$type){?>
	<td>
		<?php echo Html::dropDownList($class."[sender]",null,ArrayHelper::map($senders,'id','name'),['prompt'=>'Выберите клиента','class'=>'form-control app_sender']);?>
	</td>
	<?php }else{ echo "<td></td>";}?>

	<!-- Наименование -->
	<td>
		<?php echo Html::textInput($class."[info]",null,['class'=>'app_info form-control']);?>
	</td>

	<!-- Упаковка -->
	<?php if(!$type){?>
	<td>
		<?php echo Html::textInput($class."[count_place]",null,['class'=>'app_place form-control']);?>
	</td>
	<td>
		<?php echo Html::dropDownList($class."[package]",null,ArrayHelper::map($packages,'id','title'),['prompt'=>'Выберите упаковку','class'=>'form-control app_package']);?>
	</td>
	<?php }else{echo "<td colspan='2'></td>";} ?>
	
	<!-- Вес -->
	<?php if(!$type){?>
	<td>
		<?php echo Html::textInput($class."[weight]",null,['class'=>'form-control app_weight compute_sum compute_weight']);?>
	</td>
	<?php }else{ ?>
	<td>
		<?php echo Html::hiddenInput($class."[weight]",1);?>
	</td>
	<?php } ?>

	<!-- Ставка -->
	<td>
		<?php echo Html::textInput($class."[rate]",null,['class'=>'app_rate compute_sum compute_rate form-control']);?>
	</td>

	<td class="summa_usa">
		<?php echo Html::textInput($class."[summa_us]",null,['class'=>'summa_us form-control']);?>
	</td>
	<td class="summa">
		
	</td>
	<td>
		<?php echo Html::textInput($class."[comment]",null,['class'=>'app_comment form-control']);?>
	</td>
	<td>
		<?php echo Html::button("<i class=\"material-icons\">close</i>",["rel"=>"tooltip",'class'=>'btn btn-danger btn-sm btn-round remove_app','data-confirm'=>'Подтвердите свои дейсвтия']);?>
	</td>
</tr>