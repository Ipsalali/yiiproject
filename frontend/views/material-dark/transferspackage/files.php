<?php

use yii\helpers\Html;
use yii\helpers\Url;

use yii\bootstrap\ActiveForm;

?>

<?php 
	if($model == null || $model->files == ""){
?>
	<div class="row">
		<div class="col-12">
			<div class="alert alert-warning">
				Файлы не обнаружены!
			</div>
		</div>
	</div>
<?php
	}else{

		$files = explode("|", $model->files);
?>
		<div class="row">
			<div class="col-12">
				<ul>
				<?php
					foreach ($files as $key => $file) {
					
						if($file == "" || $file == " ") continue;

						if(file_exists($model::$filesPath.$file)){
							?>
							<li>
								<?php 
									echo Html::a($file,['transferspackage/download','id'=>$model->id,'file'=>$file],['target'=>'_blank']);
								?>
								<span>
								<?php echo Html::a("<i class=\"material-icons\">close</i>",['transferspackage/unlinkfile','id'=>$model->id,'file'=>$file],['data-confirm'=>'Подтвердите удаление файла','class'=>'btn btn-danger btn-radius btn-sm']);
								?>
								</span>
							</li>
							<?php
						}

					}
				?>
				</ul>
			</div>
		</div>
		<?php
	}
?>
