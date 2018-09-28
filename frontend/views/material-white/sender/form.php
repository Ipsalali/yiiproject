<?php 
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;

$this->title = "Отправители";

$this->params['breadcrumbs'][] = ['label'=>"Список отправителей",'url'=>Url::to(['sender/index'])];
if(isset($model->id)){
	$this->params['breadcrumbs'][] = ['label'=>$model->name,'url'=>Url::to(['sender/read','id'=>$model->id])];
}
$this->params['breadcrumbs'][]=$this->title;
?>

<div class="card">
<?php $form = ActiveForm::begin(['id'=>'senderForm']); ?>
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
					<div class="col-4 offset-4">
				        <?php echo $form->field($model, 'name')->textInput(); ?>
				    	<?php echo $form->field($model, 'phone')->textInput(); ?>
				    	<?php echo $form->field($model, 'email')->textInput(); ?>
				   	</div>
				</div>
		    </div>
<?php ActiveForm::end(); ?>
</div>