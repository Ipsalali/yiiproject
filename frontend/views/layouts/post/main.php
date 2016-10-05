<?php

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\helpers\Html;

	$this->title = "Клиенты";
?>

<?php $this->beginContent('@app/views/layouts/main.php'); ?>

<!-- <div class="left_bar">
	<?php

	if (!Yii::$app->user->isGuest) {

    	
    	$menuItems = [
        	['label' => 'меню1', 'id' => ['/#']],
        	['label' => 'меню2', 'id' => ['/#']],
        	['label' => 'меню3', 'id' => ['/#']],
        	['label' => 'меню4', 'id' => ['/#']],
    	];
    } else {
        $menuItems[] = [
            'label' => 'Войдите в систему',
            'url' => ['/site/login'],
        ];
    }
    echo Nav::widget([
        'options' => ['class' => 'nav nav-list'],
        'items' => $menuItems,
    ]);
    
    ?>
</div> -->

<div class="base_content">
	<?=$content;?>
</div>
<div class="clear"></div>

<?php $this->endContent(); ?>