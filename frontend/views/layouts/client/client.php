<?php

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\helpers\Html;
use common\models\Client;

	

    $listClients = Client::find()->all();
?>

<?php $this->beginContent('@app/views/layouts/main.php'); ?>

<div class="base_content">
	<?=$content;?>
</div>
<div class="clear"></div>

<?php $this->endContent(); ?>