<?php

use yii\helpers\Html;
use common\helper\EmailChecker;

$this->title = "Проверка email адресов";
?>
<div class="row">
	<div class="col-xs-12">
		<h4 id="result_message"></h4>
	</div>
</div>
<div class="row">
	<div class="col-xs-12">
		<table class="table table-bordered table-sm table-hovered table-collapsed" id="tableEmails">
			<thead>
				<tr>
					<th>#</th>
					<th>Клиент</th>
					<th>Email</th>
					<th><?php echo Html::a("Проверить",['spender/check-email'],['class'=>'btn btn-primary','id'=>'checkAll']);?></th>
				</tr>
			</thead>
			<tbody>
				
			<?php
				if(isset($emails) && is_array($emails)){
					foreach ($emails as $key => $item) {
				?>
				<tr>
					<td><?php echo ++$key;?></td>
					<td><?php echo $item['full_name'];?></td>
					<td class="td-email"><?php echo $item['email'];?></td>
					<td><?php //EmailChecker::check($item['email']); ?></td>
				</tr>
				<?php
					}
				}
			?>

			</tbody>
		</table>
	</div>
</div>
<?php
	$js = <<<JS
		var sendReq = 0;
		var check = function(email){
			if(!email) return false;
			//if(sendReq) return 0;

			var action = $("#checkAll").attr("href");
			var parent = $(email).parents("tr");
			var value = email.text();
			var data = {
				email:value
			};
			

			$.ajax({
				url:action,
				data:data,
				type:"GET",
				dataType:"json",
				beforeSend:function(){
					sendReq = 1;
				},
				success:function(json){
					var v = json.hasOwnProperty("success") ? json.success && 1 : false;
					if(!v){
						email.addClass("danger");
						email.removeClass("success");
					}else{
						email.removeClass("danger");
						email.addClass("success");
					}
				},
				error:function(e){
					console.log(e);
				},
				complete:function(){
					sendReq = 0;
				}
			});

			
		};


		$("#checkAll").click(function(event){
			event.preventDefault();
			var ems = $("#tableEmails .td-email");
			var total = ems.length;
			
			
			for(var i = 0; i < total; i++){
				check(ems.eq(i));
			}
			// ems.each(function(){
			// 	check(ems.eq(0));
			// 	i++;
			// });

			if(total == i){
				var e_count = $("#tableEmails .td-email.error").length;
				var s_count = $("#tableEmails .td-email.success").length;
				$("#result_message").html("total: " + total + " valid: " + s_count + " erros: " + e_count);
			}
			
		});
JS;


$this->registerJs($js);
?>