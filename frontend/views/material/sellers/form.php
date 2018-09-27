<?php 
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;

$this->title = "Поставщик ". isset($model->id) ? $model->name : "";
$this->params['breadcrumbs'][] = ['label'=>"Список поставщиков",'url'=>Url::to(['sellers/index'])];
if(isset($model->id)){
    $this->params['breadcrumbs'][] = ['label'=>$model->name,'url'=>Url::to(['sellers/read','id'=>$model->id])];
}
$this->params['breadcrumbs'][]=$this->title;
?>
<div class="card">
<?php $form = ActiveForm::begin(['id'=>'sellerForm']); ?>
    <div class="card-header card-header-primary">
        <div class="row">
            <div class="col">
                <h3 class="card-title"><?php echo $this->title?></h3>
            </div>
            <div class="col text-right">
                 <?php echo Html::submitButton('Сохранить',['class' => 'btn btn-primary']); ?>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-12">
                <div class="row">
                    <div class="col-3">
                        <?php echo $form->field($model, 'username')->textInput(); ?>
                    </div>
                    <div class="col-3">
                        <?php echo $form->field($model, 'name')->textInput(); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-3">
                        <?php echo $form->field($model, 'phone')->textInput(); ?>
                    </div>
                    <div class="col-3">
                        <?php echo $form->field($model, 'email')->textInput(); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-3">
                        <?php echo $form->field($model, 'password')->textInput(["value"=>Yii::$app->getSecurity()->generateRandomString(6)]); ?>
                    </div>
                    <div class="col-3">
                        <?php 
                            if(isset($model->id) && $model->id){
                                echo Html::label("Поменять пароль","change_password");
                                echo Html::checkbox("change_password",0,['id'=>'change_password']);
                            }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php ActiveForm::end(); ?>
</div>

