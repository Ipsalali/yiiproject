<?php 
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use common\models\Organisation;

$this->title = "Организация";
?>
 
<?php if(Yii::$app->session->hasFlash('StatusDeletedError')): ?>
<div class="alert alert-error">
    There was an error deleting your post!
</div>
<?php endif; ?>
 
<?php if(Yii::$app->session->hasFlash('StatusDeleted')): ?>
<div class="alert alert-success">
    Your post has successfully been deleted!
</div>
<?php endif; ?>
<div class="alert alert-success active_change" style="display: none;"></div>
<div class="org_page">
	<?php echo Html::a('Добавить организацию', array('organisation/create'), array('class' => 'btn btn-primary')); ?>

<div class="clearfix"></div>

<div class="">
    <table class="table">
        <tr>
            <th>№</th>
            <th>Наименование организации</th>
            <th>Активный</th>
            <th>Приостановить</th>
            <th>Удалить</th>
        </tr>
        <?php if(count($orgs)){ ?>
            <?php foreach ($orgs as $key => $org) { 
                $checked = $org->active?'checked':'';
                $stoped = $org->is_stoped?'checked':'';
                ?>
                <tr>
                    <td><?php echo $key+1; ?></td>
                    <td><?php echo Html::a($org->org_name,array('organisation/read','id'=>$org->id)); ?></td>
                    <td>
                        <?php $form = ActiveForm::begin(['action'=>['organisation/toactive',['id'=>$org->id]],'id'=>'formToActive']); ?>
                            <input type="radio" name="active_org" class="toActiveOrg" id="toActiveOrg<?php echo $org->id?>" value="<?php echo $org->id;?>" <?php echo $checked;?>>
                        <?php ActiveForm::end();?>
                    </td>
                    <td>
                        <?php $form = ActiveForm::begin(['action'=>['organisation/stop',['id'=>$org->id]],'id'=>'formToStop']); ?>
                            <input type="hidden" name="stop_org" value="<?php echo $org->id;?>">
                            <input type="checkbox" name="stop_org_status" class="toStopOrg" id="toStopOrg<?php echo $org->id?>" value="<?php echo $org->id;?>" <?php echo $stoped;?>>
                        <?php ActiveForm::end();?>
                    </td>
                    <td>
                        <?php $form = ActiveForm::begin(['action'=>['organisation/remove']]); ?>
                            <input type="hidden" name="org_id" value="<?php echo $org->id;?>">
                            <input type="submit" name="remove_org" class="remove_org" value="Удалить">
                        <?php ActiveForm::end();?>
                    </td>
                </tr>
            <?php 

                } 

                $script = <<<JS

                    $(".toActiveOrg").change(function(event){

                    event.preventDefault();
                    var form = $(this).parents("form");
                    var fdata = form.serialize();
                    this_r = $(this);
                    $.ajax({
                        url:form.attr("action"),
                        type:"POST",
                        data:fdata,
                        dataType:"json",
                        beforeSend:function(){
                            $(".toActiveOrg").disabled = true;
                        },
                        success:function(json){
                            if(json['result']){
                                $("input.toActiveOrg").prop("checked",false);
                                this_r.prop("checked",true);
                            }
                            $(".active_change").text(json['text']);
                            $(".active_change").show();
                        },
                        error:function(msg){
                            console.log(msg);
                        },
                        complete:function(){
                            $(".toActiveOrg").disabled = false;
                        }
                    })
                })


                $(".toStopOrg").change(function(event){

                    event.preventDefault();
                    var form = $(this).parents("form");
                    var fdata = form.serialize();
                    this_r = $(this);
                    $.ajax({
                        url:form.attr("action"),
                        type:"POST",
                        data:fdata,
                        dataType:"json",
                        beforeSend:function(){
                            $(".toStopOrg").disabled = true;
                        },
                        success:function(json){
                            if(json['result']){
                                
                            }
                            $(".active_change").text(json['text']);
                            $(".active_change").show();
                        },
                        error:function(msg){
                            console.log(msg);
                        },
                        complete:function(){
                            $(".toStopOrg").disabled = false;
                        }
                    })
                })
JS;

            $this->registerJS($script);

            ?>
        <?php } ?>
    </table>
</div>
</div>