<?php 
	use yii\helpers\Html; 
?>
<div class="pull-right btn-group">
    <?php echo Html::a('Update', array('clientcategory/update', 'id' => $category->cc_id), array('class' => 'btn btn-primary')); ?>
    <?php echo Html::a('Delete', array('clientcategory/delete', 'id' => $category->cc_id), array('class' => 'btn btn-danger')); ?>
</div>
 
<?php echo Html::a('Категория клиентов', array('clientcategory/index'), array('class' => '')); ?>

<h1><?php echo $category->cc_title; ?></h1>
<p>Родительская категория: <?php echo $category->parent->cc_title; ?></p>
<p><?php echo $category->cc_description; ?></p>
