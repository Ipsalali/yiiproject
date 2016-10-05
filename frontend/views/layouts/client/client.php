<?php

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\helpers\Html;
use common\models\Client;

	$this->title = "Клиенты";

    $listClients = Client::find()->all();
?>

<?php $this->beginContent('@app/views/layouts/main.php'); ?>

<!-- <div class="left_bar">
	<?php if($listClients){ ?>
        <h4>Заявки клиентов</h4>
       <ul class="list-group">
           <?php foreach ($listClients as $key => $cl) { ?>
                <li class="list-group-item"><?php echo Html::a($cl->name,array("client/app","id"=>$cl->id))?></a></li>
           <?php } ?>
       </ul>
    <? } ?>
</div> -->

<div class="base_content">
	<?=$content;?>
</div>
<div class="clear"></div>

<?php $this->endContent(); ?>