<?php 
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use mihaildev\ckeditor\CKEditor;
use mihaildev\elfinder\ElFinder;

?>


<?php echo Html::a('Cтатус заявок', array('status/index'), array('class' => '')); ?>
<div class="status_create_page">
<?php $form = ActiveForm::begin(['id' => 'status_create']); ?>
    
    <div class="form-actions">
        <?php echo Html::submitButton('Сохранить',['class' => 'btn btn-primary pull-right', 'name' => 'status-create-button']); ?>
    </div>

    <?php echo $form->field($model, 'title')->textInput(array('class' => 'form-control')); ?>
    <?php echo $form->field($model, 'sort')->textInput(array('class' => 'form-control')); ?>
    
    <?php
    	echo $form->field($model, 'description')->textarea(array("class"=>'form-control'));
    ?>
    <div class="help-msg">
    	<span>Правило составления шаблона:</span>
    	<p>Для отображения данных в шаблоне используйте следующие виды конструкции</p>
    	<ul>
    		<li><span>[APP_LIST]</span> - для вывода списка наименований клиента в одной заявке;</li>
            <li><span>[APP_STATUS_DATE]</span> - для вывода даты последнего статуса;</li>
            <li><span>[APP_COUNTRY]</span> - для вывода страны поставки;</li>
            <li><span>[APP_DATE]</span> - для вывода даты заявки;</li>
            <li><span>[APP_STATUS]</span> - для вывода статуса заявки;</li>
            <li><span>[APP_COURSE]</span> - для вывода курса заявки;</li>
    	</ul>
    </div>
     <?php
    	echo $form->field($model, 'notification_template')->widget(CKEditor::className(),[
    		'editorOptions' => ElFinder::ckeditorOptions(['elfinder'],[
       		'preset' => 'standard', //разработанны стандартные настройки basic, standard, full данную возможность не обязательно использовать
        	'inline' => false, //по умолчанию false
    	       ]),
	]);
    ?>
    
    
<?php ActiveForm::end(); ?>
</div>