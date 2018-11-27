<?php 
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;
use common\models\ClientCategory;
use common\models\Organisation;


$this->title = isset($model->id) ? $model->name : "Новый клиент";

?>


<?php $form = ActiveForm::begin(['id' => 'client_create']); ?>
<div class="row">
    <div class="col-xs-6">
        <div class="client_form">
            <h3>Данные о клиенте</h3>
            <div class="row">
                <div class="col-xs-12">
                    <?php echo $form->field($model, 'full_name')->textInput(); ?>
                    <?php
                        if(isset($model->id)){
                            echo Html::hiddenInput('model_id',$model->id);
                        }
                    ?>
                </div>
            </div>

            <div class="row">
                <div class="col-xs-6">
                    <?php echo $form->field($model, 'name')->textInput(); ?>
                </div>
                <div class="col-xs-6">
                    <?php echo $form->field($model, 'phone')->textInput(); ?>
                </div>
            </div>

            <div class="row">
                <div class="col-xs-4">
                    <?php echo $form->field($model, 'payment_clearing')->textInput(); ?>
                </div>
                <div class="col-xs-4">
                    <?php echo $form->field($model, 'organisation_pay_id')->dropDownList(ArrayHelper::map(Organisation::find()->all(),'id','org_name'),['prompt'=>'Выберите организацию']); ?>
                </div>
                <div class="col-xs-4">
                    <?php echo $form->field($model, 'contract_number')->textInput(["value"=>$model->ActualContractNumber]); ?>
                </div>
            </div>

            <div class="row">
                <div class="col-xs-12">
                    <?php echo $form->field($model, 'description')->textInput(); ?>
                </div>
            </div>

            <div class="row">
                <div class="col-xs-12">
                    <?php echo $form->field($model, 'email')->textInput(); ?>
                </div>
            </div>

            <div class="row">
                <div class="col-xs-4">
                    <?php echo $form->field($model, 'client_category_id')->dropDownList(ArrayHelper::map(ClientCategory::find()->all(),'cc_id','cc_title'),['prompt'=>'Выберите категорию']); ?>
                </div>
                <div class="col-xs-4">
                    <?php echo $form->field($model, 'manager')->dropDownList(ArrayHelper::map($managers,'id','username'),['prompt'=>'Выберите менеджера']); ?>
                </div>
                
            </div>
            
        </div>
    </div>
    <div class="col-xs-6">
        <div class="row">
           
            <div class="client_form col-xs-12">
                <h3>Данные для профиля клиента</h3>
                 <?php if($mode_user_create){ ?>
                    
                    <?php
                        if(isset($freeUser) && is_array($freeUser) && count($freeUser)){
                    ?>  
                        <div class="row">
                            <div class="col-xs-12">
                                <input type="checkbox" name="use_free_client" value="1" id="use_free_client" <?php echo isset($error) && $error ? "checked" : ""?>>
                                <label for="use_free_client">Привязать к свободному пользователю?</label>
                            
                                <?php
                                    if(isset($error) && $error){
                                        ?>
                                        <p style="color: #f00;">Выберите пользователя!!!</p>
                                        <?php
                                    }
                                ?>
                            </div>

                        </div>
                        <div id="user_select" class="row" style="<?php echo isset($error) && $error ? "" : "display: none;"?>">
                            <div class="col-xs-12">
                                <?php echo $form->field($model, 'user_id')->dropDownList(ArrayHelper::map($freeUser,'id','name'),['prompt'=>'Выберите пользователя']); ?>
                            </div>
                        </div>
                        <script type="text/javascript">
                            $(function(){
                                $("#use_free_client").change(function(){
                                    if($(this).prop("checked")){
                                        $("#user_select").show();
                                        $("#new_user_block").hide();
                                        $("#signupform-email").val("test@mail.ru");
                                    }else{
                                        $("#user_select").hide();
                                        $("#new_user_block").show();
                                        $("#signupform-email").val("");
                                    }

                                })
                            })
                        </script>
                    <?php } ?>
                    <div id="new_user_block" class="row new_user_block" style="<?php echo isset($error) && !$error ? "" : "display: none;"?>">
                        <div class="col-xs-12">
                            <div class="row">
                                <div class="col-xs-6">
                                    <?php echo $form->field($user, 'email')->textInput(); ?>
                                    <?php echo $form->field($user, 'phone')->textInput(); ?>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-xs-6">
                                    <?php echo $form->field($user, 'password')->textInput(
                                array("value"=>Yii::$app->getSecurity()->generateRandomString(6))
                            ); ?>
                                </div>
                            </div>        
                        </div>
                    </div>
                    


                <?php }else {?>
                    <div class="row">
                        <div class="col-xs-6">
                            <?php echo $form->field($user, 'email')->textInput(); ?>
                            <?php echo $form->field($user, 'phone')->textInput(); ?>
                        </div>
                    </div>
                <?php } ?> 
            </div>
            
                   
        </div>
        <div class="form-actions">
                <?php echo Html::submitButton('Сохранить',['class' => 'btn btn-primary', 'name' => 'post-create-button']); ?>
        </div>
    </div>
</div>
    
<?php ActiveForm::end(); ?>

<?php
if(isset($model->id)){

$script = <<<JS
        $("#client-organisation_pay_id").change(function(event){
            var org_id = parseInt($(this).val());
            //console.log(org_id);

            if(org_id){
                $.ajax({
                    url:"/index.php?r=client/get-relation",
                    type:"POST",
                    dataType:'json',
                    data:"client_id={$model->id}"+"&org_id="+org_id,
                    success:function(json){
                        console.log(json);
                        if(json.hasOwnProperty("value"))
                            $("#client-contract_number").val(json.value);
                    }
                });
            }else{
                $("#client-contract_number").val("{$model->contract_number}");
            }

        });


JS;

   $this->registerJS($script); 
}


?>