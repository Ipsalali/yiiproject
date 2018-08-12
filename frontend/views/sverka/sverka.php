<?php

use yii\helpers\Html;
use yii\helpers\Url;
use common\helper\ArrayHelper;
use kartik\date\DatePicker;
use yii\bootstrap\ActiveForm;
use common\models\User;
use frontend\models\Autotruck;
use frontend\models\PaymentsExpenses;
use frontend\models\ExpensesManager;
use yii\helpers\StringHelper;
use common\models\Organisation;

$this->title = "Сверка";

?>

<div>
	<div class="row">
		<div class="col-xs-4">
			<h3>Отчет "Сверка"</h3>
		</div>
		<div class="col-xs-4">
			
		</div>
	</div>
	<div class="row">
		<?php $form = ActiveForm::begin(['id'=>'sverka','action'=>["sverka/index"],'method'=>'GET']); ?>
		<div class="col-xs-4">

			<?php
				$layout = <<< HTML
    				{input1}
    				{separator}
    				{input2}
HTML;
				echo DatePicker::widget([
							'name' => 'date_from',
							'name2' => 'date_to',
							'value'=> (!$data_params['date_from']) ? date("d.m.Y",time() - (86400 * 61)) : $data_params['date_from'],
							'value2'=> (!$data_params['date_to']) ? date("d.m.Y",time()) : $data_params['date_to'],
							'language'=>'ru',
							'options' => ['placeholder' => 'Начальная дата'],
							'options2' => ['placeholder' => 'Конечная дата'],
							'type' => DatePicker::TYPE_RANGE,
							'separator'=>" по ",
							'layout'=>$layout,
							'pluginOptions'=>[
								'autoclose'=>true,
        						'format' => 'dd.mm.yyyy',
        						'todayHighlight' => true
    						]
				]);
			?>
		</div>
		<?php if(!Yii::$app->user->identity->isSeller(true)  && !Yii::$app->user->can("sverka_seer") && !Yii::$app->user->can("client")){ 

				if(isset($manager->id)){
					if($manager->name){
						$val = $manager->name;
					}elseif($manager->username){
						$val = $manager->username;
					}elseif(isset($manager->client) && isset($manager->client->id)){
						$val = $manager->client->name;
					}else{
						$val = $manager->email;
					}
				}else{
					$val = "";
				}
			?>
    		<div class="col-xs-4">
    			<div id="autocomplete_block" class="">
    				<input type="hidden" name="manager" value="<?php echo $manager->id?>" id="input_manager">
    				<input type="text" name="manager_data_key" autocomplete="off" value="<?php echo $val; ?>" class="form-control" id="manager_autocomplete">
    				<div class="autocomplete_data" data-block="0">
    					<ul id="autocomplete_items">
    						<?php
    							if(is_array($expensesPeople) && count($expensesPeople)){
    								foreach ($expensesPeople as $key => $value) {
    									?>
    										<li data-id="<?php echo $value->id?>"> <?php echo $value->name." - ".$value->email?> </li>
    									<?php
    								}
    							}
    						?>
    					</ul>
    				</div>
    			</div>
    			<script type="text/javascript">
    				$(function(){

    					$("#manager_autocomplete").focusin(function(){
    						$(".autocomplete_data").show(100);
    					});

    					$("#manager_autocomplete").focusout(function(e){
    						if(!parseInt($(".autocomplete_data").attr("data-block")))
    							$(".autocomplete_data").hide(100);
    						
    					});

    					$("#manager_autocomplete").keyup(function(){
    						var val = $(this).val();

    						if(val.length < 2) return;

    						var action = "<?php echo Url::to(['sverka/expenses-people-by-key'])?>";

    						$.ajax({
    							url:action,
    							type:"POST",
    							data:"key="+val,
    							dataType:"json",
    							before:function(){},
    							success:function(json){
    								if(json.hasOwnProperty("managers")){
    									var managers = json.managers;
    									if(managers.length){
    										var html = "";
    										$.each(managers,function(i,item){
    											var name = item.email;
    											
    											if(item.name){
    												name= item.name;
    											}else if(item.username){
    												name = item.username;
    											}
    											
    											html += "<li data-id='"+item.id+"'>" + name + "</li>";
    										})

    										$("#autocomplete_items").html(html);
    									}
    								}
    							},
    							error:function(e){
    								console.log(e);
    							},
    							complete:function(){}
    						})
    					});



    					$("#autocomplete_items").hover(function(){
    						$(".autocomplete_data").attr("data-block",1);
    					},function(){
    						$(".autocomplete_data").attr("data-block",0);
    					});



    					$("#autocomplete_items").on("click","li",function(){
    						var id = parseInt($(this).attr("data-id"));
    						$("#input_manager").val(id);

    						$("#manager_autocomplete").val($(this).text());
    						$(".autocomplete_data").attr("data-block",0);
    						$(".autocomplete_data").hide(100);
    					})

    					
    				})
    			</script>
    		</div>
		<?php }else{
		    ?>
		        <input type="hidden" name='manager' value='<?php echo Yii::$app->user->identity->id; ?>'>
		    <?php
		}?>

		<div class="col-xs-4">
			<input type="submit" class="btn btn-primary" value="Найти">
		</div>
		<?php Activeform::end(); ?>
	</div>

	<div class="tabs"  style="margin-top: 50px;">
		<ul class="nav nav-tabs">
  			<li class="active"><a data-toggle="tab" href="#sverka_app">Доставки</a></li>
  			<li><a data-toggle="tab" href="#sverka_transfer">Переводы</a></li>
		</ul>
	</div>

<div class="tab-content">
	<div id="sverka_app" class="tab-pane fade in active">
	<?php if(count($sverka)){ ?>
	<div class="row">
		<div class="col-xs-12">
			<h4>
			<?php if(!Yii::$app->user->identity->isSeller(true) && !Yii::$app->user->can("sverka_seer") && !Yii::$app->user->can("client")){?>
			Расходы 
				<?php if($manager->name) {
					echo $manager->name;
				}else{
					$cl = $manager->client;
					if(isset($cl->id)){
						echo $cl->name;
					}else{
						echo $manager->username;
					}
				}
				?> от <?php echo date("d.m.y",strtotime($data_params['date_from']))?> по <?php echo date("d.m.y",strtotime($data_params['date_to']))?>
			    
			<?php }else{ ?> 
				Ваши расходы  от <?php echo date("d.m.y",strtotime($data_params['date_from']))?> по <?php echo date("d.m.y",strtotime($data_params['date_to']))?>
			<?php } ?>
			&nbsp&nbspСумма расходов за указанный период: <span id="common_sum_expenses_by_period"></span>&nbsp$</h4>
			
			<?php 
			    if(!Yii::$app->user->identity->isSeller(true) && !Yii::$app->user->can("sverka_seer") && !Yii::$app->user->can("client")){
			    //Показываем только для менеджера
			?>
			    <p>Оплата по безналу: <?php echo $card_percent;?> % &nbsp&nbsp&nbsp&nbsp Средний курс: <?php echo $average_course?></p>
			<?php } ?>
		</div>
	</div>

	<?php if(!Yii::$app->user->can("client")){ ?>
	<div class="row">
		<div class="col-xs-3">
			<button id="add_pay" class="btn btn-success">Добавить оплату</button>
		</div>
	</div>
	<?php } ?>
	<div class="row">
		<div class="col-xs-12">
		<?php $form = ActiveForm::begin(['id'=>'addPaymentsManager','action'=>["sverka/addpaymentsmanager"]])?>
			
			<table class="table table-bordered table-hover" style="text-align: center;" id="table_pay">
				<tr>
					<th>#</th>
					<th>Дата</th>
					<th class="th_sum">Сумма $</th>
					
					<!-- <th>Наличные($)</th> -->
					<th class="th_group">Сумма (руб)</th>
					<th class="th_group">Сумма Б/Н (руб)</th>
					<th class="th_group th_payment">Оплата</th>
					<th class="th_course">Курс</th>
					<th>Организация</th>
					<th style="width: 150px;">В отчет</th>
					<th>Комментарий</th>
					<th>Действие</th>
				</tr>


				<?php  $com=0; $cExpens = 0; foreach ($sverka as $key => $sv) { 
						if((int)$sv['type'] == 1){
							$model = PaymentsExpenses::findOne($sv['id']);
						}elseif((int)$sv['type'] == 2){
							$model = Autotruck::findOne($sv['id']);
						}elseif(!(int)$sv['type']){
							$model = ExpensesManager::findOne($sv['id']);
						}
					
						//Новый изменения
						$cExpens += ((int)$sv['type'] == 1) ? 0 : $sv['sum'];

						$classType = (int)$sv['type'] == 1 ? "type_negative" : "type_positive";
					?>
					<tr class="<?php echo ($sv['type'])? "pay_row":"exp_row"; ?>">
						<td><?php echo ++$key;?></td>
						<td class="td_input_date" data-update="<?php echo $model instanceof PaymentsExpenses ? 1 : 0?>">
							<?php if($model instanceof ExpensesManager){
									echo Html::a($model->autotruck->name."&nbsp".date("d.m.Y",strtotime($sv['date'])),array("autotruck/read",'id'=>$model->autotruck_id),array("target"=>"_blank"));
								}elseif($model instanceof Autotruck){
									echo Html::a($model->name."&nbsp".date("d.m.Y",strtotime($sv['date'])),['autotruck/read','id'=>$model->id],['target'=>'_blank']);
								}else{
									echo date("d.m.Y",strtotime($sv['date']));
								} 
							?>
						</td>
						<td class='td_input_sum <?php echo $classType?>'><?php 
								
								
								if((int)$sv['type'] == 1){
								    
								    
								    //Для старых записей у которых нет сум руб и сум без нла и нет курса
								    if(!(int)$sv['course'] && isset($sv['sum_cash']) && !(int)$sv['sum_cash'] && isset($sv['sum_card']) && !(int)$sv['sum_card']){
								        
								        echo "-".$sv['sum'];
								        
								    }elseif(!(int)$sv['toreport'] || !(int)$sv['course']){
								   
								        echo "-".$sv['sum'];
								    }
								    
								}else{
								    echo "+".$sv['sum'];
								}
								
							?>
						</td>

						<td class='td_input_sum_cash'>
							<?php 
								if($sv['type'] == 1){

									//Наличные руб
									//if($sv['toreport'] == 2){
									//	$z =   "-";
										//echo $sv['sum_cash'] > 0 ? $z.$sv['sum_cash'] : $sv['sum_cash'];
									//}
								}elseif($sv['type'] == 2){
									echo "+".$sv['sum_cash'];
								}
							?>
						</td>

						<td class='td_input_sum_card'>
							<?php 
								if($sv['type'] == 1){
									
									//безнал
									//if($sv['toreport'] == 3){
									//	$z =   "-";
									//	//echo $sv['sum_card'] > 0 ? $z.$sv['sum_card'] : $sv['sum_card'];
									//}

								}elseif($sv['type'] == 2){
									echo "+".$sv['sum_card'];
								}
							?>
						</td>

						<td class="td_payment">
							<?php 
							    
							    if($sv['type'] == 1){
							        // Сумма в $
    								if($sv['toreport'] == 1){
    								    echo  "-".$sv['sum'];
    								}elseif($sv['toreport'] == 2){
    								    echo  "-".$sv['sum_cash'];
    								}elseif($sv['toreport'] == 3){
    								    echo  "-".$sv['sum_card'];
    								}
							    }
								
								
							?>
						</td>
						<td class="td_input_course">
							<?php 
								//отображаем курс для оплат только
								echo $sv['type'] == 1 ? $sv['course'] : "";
							?>
						</td>
						
						<td class="td_select_org" data-exist_org="<?php echo $model instanceof PaymentsExpenses ? 1 : 0;?>" data-org-val="<?php echo $model instanceof PaymentsExpenses ? $model->organisation : 0;?>">
							<?php
								if($model instanceof PaymentsExpenses && $model->organisation){
									echo $model->org->org_name;
								?>

								<?php
								}
							?>
						</td>

						<td class="td_select_report"  data-report-val="<?php echo $sv['toreport']?>">
							<?php 
								$sv_report = "";
								switch ($sv['toreport']) {
									case 1:
										$sv_report = "Сумма $";
										break;
									case 2:
										$sv_report = "Сумма (руб).";
										break;
									case 3:
										$sv_report = "Сумма Б/Н (руб).";
										break;
									
									default:
										$sv_report = "";
										break;
								}

							echo $sv_report; ?>
						</td>

						<td class="td_input_comment"><?php echo $sv['comment']; ?></td>
						<td>
							<?php 
								if((int)$sv['type'] != 2){
									$action = ($sv['type']) ? "sverka/removepayajax":"autotruck/removeexpajax";
							?>
								<a class="btn btn-primary sverka_update_btn" data-state="0" data-id="<?php echo $sv['id']?>" data-model="<?php echo StringHelper::basename(get_class($model));?>"><i class="glyphicon glyphicon-pencil"></i></a>
							    <?php if(!Yii::$app->user->can("client")){?>
								    <button type="submit" data-action="<?php echo $action?>" data-id="<?php echo $sv['id']?>" class="btn btn-danger remove_exists_payexp">X</button>
							    <?php } ?>
							<?php } ?>
						</td>
					</tr>
				<?php  
						if(!$sv['type']){
							$com += $sv['sum']; 
						}elseif((int)$sv['type'] == 1){
							$com -= $sv['sum']; 
						}elseif((int)$sv['type'] == 2){
							$com -= $sv['sum']; 
						}
					} ?>
					
				
				<!-- <tr>
					<td colspan="2"><strong>Сумма расходов за указанный период</small></strong></td>
					<td colspan="8"><strong><?php echo $cExpens;?></strong> $</td>
				</tr> -->
				<script type="text/javascript">
					$(function(){
						var common_sum_expenses_by_period = <?php echo $cExpens;?>;
						$("#common_sum_expenses_by_period").text(common_sum_expenses_by_period);
					});
				</script>

				<tr id="foot_row">
					<td colspan="2"><strong>Итого <small>(Все расходы и оплаты в системе)</small></strong></td>
					<td colspan="1" style="text-align: left;"><strong>&nbsp<?php echo sprintf("%.2f",$totalSverka['sum'] ); ?> $</strong></td>
					<td colspan="1" style="text-align: left;"><strong>&nbsp<?php echo sprintf("%.2f",$totalSverka['sum_cash'] ); ?> Руб.</strong></td>
					<td colspan="7" style="text-align: left;"><strong>&nbsp<?php echo sprintf("%.2f",$totalSverka['sum_card'] ); ?> б/н Руб.</strong></td>
				</tr>
			</table>
			<div class="row">
				<div class="col-xs-12" style="margin-bottom: 10px; text-align: right;">
					<button id="submit_addPaymentsManager" style="display: none;" class="btn btn-primary">Сохранить</button>
				</div>
			</div>
			<?php ActiveForm::end();?>
		</div>
		
	</div>
	<?php }elseif($manager->id){ ?>
		<div class="row">
    		<?php if(!Yii::$app->user->identity->isSeller(true) && !Yii::$app->user->can("sverka_seer") && !Yii::$app->user->can("client")){?>
    			<div class="col-xs-12">
    				<h4>У <?php echo $manager->name ?$manager->name:"Не указано имя (".$manager->username.")"?>  нет расходов по периоду от <?php echo date("d.m.y",strtotime($data_params['date_from']))?> по <?php echo date("d.m.y",strtotime($data_params['date_to']))?></h4>
    			</div>
    		<?php }else{?> 
    			<div class="col-xs-12">
    				<h4>У вас нет расходов на период от <?php echo date("d.m.y",strtotime($data_params['date_from']))?> по <?php echo date("d.m.y",strtotime($data_params['date_to']))?></h4>
    			</div>
    		<?php } ?>
	    </div>
	<?php } ?>
</div>
<div id="sverka_transfer" class="tab-pane fade in">
	<?php 
		echo $this->render("sverka_transfer",[
			"manager"=>$manager,
			'orgs'=>$orgs,
			"sellers"=>$sellers,
            "data_params"=>$data_params,
            'client'=>$client,
			'sverka'=>$clientSverkaByTransfer
		]);
	?>
</div>
</div>
</div>

<?php if(count($sverka) && $manager->id){?>
<input type='hidden' name="card_percent" id="card_percent" value="<?php echo $card_percent?>" />
<script type="text/javascript">
	$(function(){
        
        var display_pay_input = function(select){
            
            if(select){
                var id = parseInt(select.val());
                var tr = select.parents("tr");
                var sum_cash = tr.find(".td_input_sum_cash input").attr("readonly",true);
                var sum_card = tr.find(".td_input_sum_card input").attr("readonly",true);
                var pay = tr.find(".td_payment input").attr("readonly",true);
                
                if(id == 1){
                    pay.attr("readonly",false);
                }else if(id == 2){
                    sum_cash.attr("readonly",false);
                }else if(id == 3){
                    sum_card.attr("readonly",false);
                }
                
            }
        }
        //Определяем какой вид оплаты отображаем
        $("body").on("change",".td_select_report select",function(){
           //display_pay_input($(this));
        });
        
		//Расчет платежей при изменении суммы $
		$("body").on("keyup",".td_payment input",function(){

			var sum = $(this).val();
			var course = $(this).parents("tr").find(".td_input_course input").val();
			var card_percent = $("#card_percent").val();
			console.log("sum  = "+ sum + "; course = "+course+ "; percent = " + card_percent);
			
			
            
			//Расчет Наличные Руб 
			var sum_cash = parseFloat(course * sum).toFixed(2);
			$(this).parents("tr").find(".td_input_sum_cash input").val(sum_cash);
	
			var comission =  parseFloat(sum_cash * card_percent/100);
			var sum_card = parseFloat(Number(sum_cash) + Number(comission)).toFixed(2);
			
			$(this).parents("tr").find(".td_input_sum_card input").val(sum_card);
		});
		
		//Расчет платежей при изменении Сумма руб
		$("body").on("keyup",".td_input_sum_cash input",function(){

			var sum_cash = $(this).val();
			var course = $(this).parents("tr").find(".td_input_course input").val();
			var card_percent = $("#card_percent").val();
			console.log("sum_cash  = "+ sum_cash + "; course = "+course+ "; percent = " + card_percent);
			
            
			//Расчет Наличные $ 
			var sum = parseFloat(sum_cash/course).toFixed(2);
			$(this).parents("tr").find(".td_payment input").val(sum);
	
			var comission =  parseFloat(sum_cash * card_percent/100);
			var sum_card = parseFloat(Number(sum_cash) + Number(comission)).toFixed(2);
			$(this).parents("tr").find(".td_input_sum_card input").val(sum_card);
		});
		
		//Расчет платежей при изменении безнал руб
		$("body").on("keyup",".td_input_sum_card input",function(){

			var sum_card = $(this).val();
			var course = $(this).parents("tr").find(".td_input_course input").val();
			var card_percent = $("#card_percent").val();
			
			console.log("sum_card  = "+ sum_card + "; course = "+course+ "; percent = " + card_percent);
			
            
			//Расчет Наличные руб
			var sum_cash = parseFloat(sum_card/(1 + card_percent/100)).toFixed(2);
			$(this).parents("tr").find(".td_input_sum_cash input").val(sum_cash);
	        
	        //Расчет суммы $
			var sum = parseFloat(Number(sum_cash)/course).toFixed(2);
			$(this).parents("tr").find(".td_payment input").val(sum);
		});


		//Расчет платежей при изменении курса
		$("body").on("keyup",".td_input_course input",function(){
			
			return ;
			var course = $(this).val();
			var sum = $(this).parents("tr").find(".td_payment input").val();
			console.log("sum  = "+ sum + "; course = "+course);
			//Расчет Наличные Руб 
			var sum_cash = parseFloat(course * sum).toFixed(2);
			$(this).parents("tr").find(".td_input_sum_cash input").val(sum_cash);
		});


		var row_count = <?=count($sverka)?>;

		var generate_exp_row = function(n){
			var ntr = '<tr class="pay_row new_row">'+
			           '<td>-'+
			           '<input type="hidden" name="PaymentsExpenses['+n+'][manager_id]" value="<?php echo $manager->id?>">'+

			           '<td><input type="date" class="form-control" name="PaymentsExpenses['+n+'][date]" value="<?php echo date('Y-m-d',strtotime($data_params['date_to']))?>"></td>';
			

			var ntd_info = "<td class='td_payment'><input type='text' name='PaymentsExpenses["+n+"][sum]' class=\'sum form-control\'></td>";
			var ntd_course = "<td class='td_input_course'><input type='text' name='PaymentsExpenses["+n+"][course]' class=\'course form-control\'></td>";
			var ntd_comment = "<td class='td_input_comment'><input type='text' name='PaymentsExpenses["+n+"][comment]' class=\'pay_comment form-control\'></td>";

			var ntd_sum_cash = "<td class='td_input_sum_cash'><input type='text' name='PaymentsExpenses["+n+"][sum_cash]' class=\'sum_cash form-control\' style='display:none' readonly></td>";

			var ntd_sum_card = "<td class='td_input_sum_card'><input type='text' name='PaymentsExpenses["+n+"][sum_card]' class=\'sum_card form-control\' style='display:none' readonly></td>";

			var	ntd_org = '<td><select name="PaymentsExpenses['+n+'][organisation]" class=\'form-control\'>';
			ntd_org += '<option value="">Выберите организацию</option>';
			<?php foreach ($orgs as $key => $o) { ?>

				ntd_org += '<option value="<?=$o->id?>"><?=$o->org_name?></option>';

			<?php } ?>
			ntd_org += '</select></td>';

			var ntd_report = '<td class="td_select_report"><select name="PaymentsExpenses['+n+'][toreport]" class=\'form-control\'>';
				ntd_report += "<option value='1'>Сумма $</option>";
				ntd_report += "<option value='2'>Сумма (руб.)</option>";
				ntd_report += "<option value='3'>Сумма Б/Н (руб.)</option>";
			ntd_report += "</select></td>";

			
				
			var tmpTd = "<td class='tmpTd'></td>";

			ntr += "<td></td>"+ntd_sum_cash+ntd_sum_card+ntd_info+ntd_course+ntd_org+ntd_report+ntd_comment;
			ntr += "<td><a class='btn btn-danger remove_pay'>X</a></td>";

			ntr += '</tr>';

			return ntr;
		}

		var  displaySplitNegativeAndPositive = function(){
			
			//Временный столбец для наглядности
			if(!$(".tmpTh_sum").length && !$(".tmpTf").length){
			   	
			   	$(".th_sum").before($("<th/>").addClass("tmpTh_sum").text("Реализация $"));
			 	$("#foot_row td").eq(0).after($("<td/>").addClass("tmpTf"));
			 	
			 	var temp_td = $("<td/>").addClass("tmpTd");
				$(".td_input_sum").before(temp_td);

				$(".td_input_sum").each(function(){
					if($(this).hasClass("type_positive")){
						$(this).prev().text($(this).text());
						$(this).text("");
					}
				});	
			}
	
		}

		var displayMixerNegativeAndPositive = function(){
			if($(".tmpTh_sum").length && $(".tmpTd").length && $(".tmpTf").length){
			   
				$(".td_input_sum").each(function(){
					if($(this).hasClass("type_positive")){
						$(this).text($(this).prev().text());
					}
				});

				$(".tmpTh_sum,.tmpTf,.tmpTd").remove();
			}
		}

		//Добавление новой строки для оплаты
		$("#add_pay").click(function(event){
			event.preventDefault();
			row_count +=1;
			
			//displaySplitNegativeAndPositive();

			$("#table_pay #foot_row").before(generate_exp_row(row_count));

			$("#submit_addPaymentsManager").show();
		});


		//Удаление новой добавленной строки
		$("#table_pay").on("click",'.remove_pay',function(){
			$(this).parents('.new_row').remove();
			if(!$("tr.new_row").length && !$(".tr_form_actived").length){
				$("#submit_addPaymentsManager").hide();
				//displayMixerNegativeAndPositive();
			}
		});

		//Сохранение добавленных оплат
		$("#submit_addPaymentsManager").click(function(event){

			event.preventDefault();
			var form = $("#addPaymentsManager");
			var valid = true;
			if(!row_count){
				alert("Не добавлено ни одной оплаты");
				event.preventDefault();
			}

			//Проверяем cost
			if($("input.sum").length){
				$("input.sum").each(function(e,i){
					if($(this).val() == '' || !$(this).val()){
						$(this).css('outline','1px solid #f00');
						$(this).attr('placeholder','Укажите сумму');
						valid = false;
					}else{
						$(this).css('outline','1px solid #0f0');
					}
				})
			}
			if(!valid){
				return;
			}
			var formData = form.serialize();
			if(valid){
				$.ajax({
					url:"index.php?r=sverka%2Faddpaymentsmanager",
					type:"POST",
					data:formData,
					dateType:'json',
					beforeSend:function(){

					},
					success:function(json){
						if(json['result']){
							location = window.location.href;
						}else{
							alert("Оплата не добавлена");
							console.log(json['messages']);
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
		//Удаление наименовании
		$("#table_pay").on("click",".remove_exists_payexp",function(event){
			
			event.preventDefault();
			var id = parseInt($(this).data("id"));
			var action = $(this).data("action");
			var dataForm = "id="+id;
			var r_rw = $(this).parents('tr');

			if(id && action && window.confirm('Вы действительно хотите удалить выделенный объект?')){
				$.ajax({
					url:"index.php?r="+action,
					type:"POST",
					data: dataForm,
					datetype:'json',
					beforeSend:function(){
						console.log('before');
					},
					success:function(json){
						if(json['error']){
							alert(json['error']['text']);
						}else{
							
							location = window.location.href;
							r_rw.remove();
						}
					},
					error:function(msg){
						console.log(msg.StatusText);
						console.log(msg.responsive);
					},
					complete:function(){
						console.log('complete');
					}
				});
			}
		})

		//формирование формы редактирование для оплат и расходов 
		$("#table_pay").on("click",".sverka_update_btn",function(event){
			var $this = $(this);
			
			var model = $this.attr("data-model");
			var id =  $this.attr("data-id");
			var state =  parseInt($this.attr("data-state"));
			
			var td_input_payment = $(this).parent().siblings(".td_payment");
			var td_input_payment_course = $(this).parent().siblings(".td_input_course");
			var td_input_sum_cash = $(this).parent().siblings(".td_input_sum_cash");
			var td_input_sum_card = $(this).parent().siblings(".td_input_sum_card");
			
			var td_input_comment = $(this).parent().siblings(".td_input_comment");
			var td_input_date = $(this).parent().siblings(".td_input_date");
			var td_select_org = $(this).parent().siblings(".td_select_org");
			var td_select_report = $(this).parent().siblings(".td_select_report");
			
			if(model && id && !state){
				$this.parents("tr").addClass("tr_form_actived");
				$this.attr("data-state",1);
				
				$this.attr("data-old_sum",td_input_payment.text());
				
				$this.attr("data-old_sum_course",td_input_payment_course.text());
				$this.attr("data-old_sum_cash",td_input_sum_cash.text());
				$this.attr("data-old_sum_card",td_input_sum_card.text());

				$this.attr("data-old_comment",td_input_comment.text());
				
			
				var input_sum = "<input type='text' name='"+model+"["+id+"][sum]' value='"+Math.abs(td_input_payment.text())+"' class='form-control'><input type='hidden' name='"+model+"["+id+"][id]' value='"+id+"'>";

				var input_course = "<input type='text' name='"+model+"["+id+"][course]' value='"+Math.abs(td_input_payment_course.text())+"' class='form-control'><input type='hidden' name='"+model+"["+id+"][id]' value='"+id+"'>";


				var input_sum_cash = td_input_sum_cash.text() + "<input type='text' name='"+model+"["+id+"][sum_cash]' value='"+Math.abs(td_input_sum_cash.text())+"' class='form-control' style='display:none' readonly>";

				var input_sum_card = td_input_sum_card.text() + "<input type='text' name='"+model+"["+id+"][sum_card]' value='"+Math.abs(td_input_sum_card.text())+"' class='form-control' style='display:none' readonly>";

				//Редакт. орг если это оплата
				if(parseInt(td_select_org.attr("data-exist_org"))){

					$this.attr("data-old_org",td_select_org.text());

					var	ntd_org = '<select name="'+model+'['+id+'][organisation]" class=\'form-control\'>';
					ntd_org +='<option value="">Выберите организацию</option>';
					<?php foreach ($orgs as $key => $o) { ?>
						ntd_org += '<option value="<?=$o->id?>"><?=$o->org_name?></option>';
					<?php } ?>
					ntd_org +='</select>';

					td_select_org.html(ntd_org);
					td_select_org.find("select").val(parseInt(td_select_org.attr("data-org-val")));
				}

				if(parseInt(td_select_report.attr("data-report-val"))){

					$this.attr("data-old-report-val",td_select_report.text());

					var	ntd_report = '<select name="'+model+'['+id+'][toreport]" class=\'form-control\'>';
						ntd_report += "<option value='1'>Сумма $</option>";
						ntd_report += "<option value='2'>Наличные(руб.)</option>";
						ntd_report += "<option value='3'>Безнал(руб.)</option>";
						ntd_report +="</select>";
					

					td_select_report.html(ntd_report);
					td_select_report.find("select").val(parseInt(td_select_report.attr("data-report-val")));
					
				}


				//Редакт дату если это оплата
				if(parseInt(td_input_date.attr("data-update"))){
					$this.attr("data-old_date",td_input_date.text());

					var input_date = '<input type="date" class="form-control" name="'+model+'['+id+'][date]" value="'+td_input_date.text().trim()+'">';

					td_input_date.html(input_date);
				}

				var input_com = "<input type='text' name='"+model+"["+id+"][comment]' value='"+td_input_comment.text()+"' class='form-control'>";
				td_input_payment.html(input_sum);
				td_input_payment_course.html(input_course);
				td_input_sum_cash.html(input_sum_cash);
				td_input_sum_card.html(input_sum_card);

				td_input_comment.html(input_com);
				
				//if(td_select_report.find("select").length){
				    //display_pay_input(td_select_report.find("select"));
				//}
				
				$this.find("i").removeClass("glyphicon-pencil");
				$this.find("i").addClass("glyphicon-resize-small");
				$("#submit_addPaymentsManager").show();
			}else{
				$this.parents("tr").removeClass("tr_form_actived");
				
				var old_sum = $this.attr("data-old_sum");
				var old_sum_course = $this.attr("data-old_sum_course");
				var old_sum_cash = $this.attr("data-old_sum_cash");
				var old_sum_card = $this.attr("data-old_sum_card");
				
				var old_com = $this.attr("data-old_comment");
				var old_org = $this.attr("data-old_org");
				var old_report = $this.attr("data-old-report-val");
				var old_date = $this.attr("data-old_date");
				$this.attr("data-state",0);

				td_input_payment.html(old_sum);
				td_input_sum_card.html(old_sum_card);
				
				td_input_payment_course.html(old_sum_course);
				
				td_input_sum_cash.html(old_sum_cash);

				td_input_comment.html(old_com);

				td_select_report.html(old_report);
				td_select_org.html(old_org);
				td_input_date.html(old_date);

				$this.find("i").addClass("glyphicon-pencil");
				$this.find("i").removeClass("glyphicon-resize-small");
				if(!$(".tr_form_actived").length && !$(".new_row").length){
					$("#submit_addPaymentsManager").hide();
				}
			}
		});
	})
</script>
<?php } ?>