<?php 

use yii\helpers\Html;
use yii\helpers\Url;

use common\models\Currency;

$client_id = isset($client->id) ? $client->id : 0;
$contragents = array();

foreach ($orgs as $o) {
	$contragents['organisation#'.$o['id']] = $o['org_name'];
}

foreach ($sellers as $s) {
	$contragents['seller#'.$s['id']] = $s['name'];
}

if(!$client_id) return false;
//Выход из скрипта, если выбран не клиент
?>

<div class="row">
	<div class="col-xs-2">
		<?php
			echo Html::a("Добавить оплату",['/sverka/pay-form-transfer-client'],['class'=>'btn btn-success','id'=>'btnPayFormClientTransfer']);
		?>
	</div>
</div>
<div class="row">
	<div class="col-xs-12">
			<form id="from_transfer_sverka" action="<?php echo Url::to(['sverka/save-client-payment-transfer'])?>" method="POST" >
				<table class="table table-bordered table-hovered" id="transfer_sverka">
					<thead>
						<tr>
							<th>#</th>
							<th>Дата</th>
							<th>Валюта</th>
							<th>Курс</th>
							<th>Сумма</th>
							<th>Сумма (руб)</th>
							<th>Контрагент</th>
							<th>Комментарий</th>
							<th></th>
						</tr>
					</thead>
					<?php
					if(isset($sverka) && count($sverka)){
						$i = 0;
						foreach ($sverka as $key => $row) {

							$rData = isset($row[0]) ? $row[0] : $row;
							$rowspan = isset($row[1]) ? 2 : 1;
							$isPay = $rData['type'] == 1 && 1;
							$class = !$isPay ?  "transfer" : "pay";
							?>
							<tr class="<?php echo $class?>">
								<td rowspan="<?php echo $rowspan ?>"><?php echo ++$i?></td>
								<td rowspan="<?php echo $rowspan ?>"><?php echo Html::encode($rData['date'])?></td>
								<td><?php echo Currency::getCurrencyTitle($rData['currency'])?></td>
								<td><?php echo Html::encode($rData['course'])?></td>
								<td><?php echo Html::encode($rData['sum'])?></td>
								<td><?php echo Html::encode($rData['sum_ru'])?></td>
								<td>
								<?php 
									if((int)$rData['contractor_org'] > 0 && array_key_exists("organisation#".(int)$rData['contractor_org'], $contragents)){
										echo $contragents["organisation#".(int)$rData['contractor_org']];
									}elseif((int)$rData['contractor_seller'] > 0 && array_key_exists("seller#".(int)$rData['contractor_seller'], $contragents)){
										echo $contragents["seller#".(int)$rData['contractor_seller']];
									} 
								?>	
								</td>
								<td><?php echo Html::encode($rData['comment'])?></td>
								<td>
									<?php
										if($isPay){
											
											echo Html::a("<i class=\"glyphicon glyphicon-pencil\"></i>",['sverka/pay-form-transfer-client','id'=>$rData['id']],['class'=>'btn btn-primary formEditClientTransferPayments']);
											
										}
										
									?>
								</td>
							</tr>
							<?php
								if(isset($row[1])){
									$rData = $row[1];
							?>
								<tr class="<?php echo $class?>">
									<td><?php echo Currency::getCurrencyTitle($rData['currency'])?></td>
									<td><?php echo Html::encode($rData['course'])?></td>
									<td><?php echo Html::encode($rData['sum'])?></td>
									<td><?php echo Html::encode($rData['sum_ru'])?></td>
									<td><?php echo ""; ?></td>
									<td><?php echo Html::encode($rData['comment'])?></td>
									<td></td>
								</tr>
							<?php
								}
							?>
							<?php
						}
					}
					?>
				</table>
				<?php echo Html::submitButton("Сохранить",['class'=>'btn btn-primary','id'=>'btnSubmitTransferClientSverka','style'=>'display:none']);?>
			</form>		
	</div>
</div>
<?php

$script = <<<JS

	var client_id = $client_id;

	$("#btnPayFormClientTransfer").click(function(event){
		event.preventDefault();
		var action = $(this).attr("href");
		var count = $("#transfer_sverka").find("tr.pay").length;
		$.ajax({
			url:action,
			type:"GET",
			data:{
				number:count
			},
			dataType:"json",
			beforeSend:function(){

			},
			success:function(json){
				if(json.hasOwnProperty("html")){
					$("table#transfer_sverka tbody").append(json.html);
					$("#btnSubmitTransferClientSverka").show();
				}
			},
			error:function(msg){
				console.log(msg);
			},
			complete:function(){

			}
		});
	});


	$("body").on("click",".formEditClientTransferPayments",function(event){
		event.preventDefault();

		var action = $(this).attr("href");
		var row = $(this).parents("tr.pay");
		var rowContents = row.html();
		var count = parseInt(row.find("td").eq(0).text());
		row.attr("oldContext",rowContents);
		$.ajax({
			url:action,
			type:"GET",
			data:{
				number:count
			},
			dataType:"json",
			beforeSend:function(){

			},
			success:function(json){
				if(json.hasOwnProperty("html")){
					row.html(json.html);
					row.addClass("pay_form");
					$("#btnSubmitTransferClientSverka").show();
				}
			},
			error:function(msg){
				console.log(msg);
			},
			complete:function(){

			}
		});
	});
	
	$("body").on("click",".closePayClientForm",function(event){
		event.preventDefault();
		var row = $(this).parents("tr.pay_form");
		var oldContext = row.attr("oldContext");
		row.removeClass("pay_form");
		row.html(oldContext);
		if(!$("#transfer_sverka").find("tr.pay_form").length){
			$("#btnSubmitTransferClientSverka").hide();
		}
	});


	$("body").on("click",".remove_pay_transfer_client",function(event){
		event.preventDefault();

		$(this).parents("tr.pay_form").remove();
		if(!$("#transfer_sverka").find("tr.pay_form").length){
			$("#btnSubmitTransferClientSverka").hide();
		}
	});


	$("#from_transfer_sverka").submit(function(event){
		event.preventDefault();
		var action = $(this).attr("action");
		var fData = $(this).serialize();
		var formHasError = false;
		$("#transfer_sverka").find("tr.pay_form input.req").each(function(){
			if(!$(this).val() || $(this).val() == "" || $(this).val() == 0){
				$(this).addClass("inputNotValid");
				formHasError = true;
			}else{
				$(this).removeClass("inputNotValid");
			}
		});

		if(formHasError){
			return;
		}

		if(client_id){
			$.ajax({
				url:action,
				type:"POST",
				data:fData+"&client_id="+client_id,
				dataType:"json",
				beforeSend:function(){

				},
				success:function(json){
					if(json.hasOwnProperty("success") && json.success){
						location = window.location.href;
					}
				},
				error:function(msg){
					
					console.log(msg);

				},
				complete:function(){

				}
			});
		}
	});



	$("body").on("click",".delete_pay_transfer_client",function(event){
		event.preventDefault();

		if(!confirm("Подтвердите свои действия!")){
			return false;
		}
		var id = parseInt($(this).data("id"));
		var action = $(this).attr("href");
		$.ajax({
				url:action,
				type:"POST",
				data:{
					id:id
				},
				dataType:"json",
				beforeSend:function(){

				},
				success:function(json){
					if(json.hasOwnProperty("success") && json.success){
						location = window.location.href;
					}
				},
				error:function(msg){
					
					console.log(msg);

				},
				complete:function(){

				}
			});
	});
JS;


$this->registerJs($script);
?>