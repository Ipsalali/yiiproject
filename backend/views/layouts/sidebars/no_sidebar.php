<?php

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\helpers\Html;

	$this->title = "Вход";
?>

<?php $this->beginContent('@app/views/layouts/main.php'); ?>
<div class="container">
	<?=$content;?>
</div>
<?php $this->endContent(); ?>