<?php

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\helpers\Html;

	$this->title = "Настройки";
?>

<?php $this->beginContent('@app/views/layouts/main.php'); ?>

<div class="left_bar">
	<?php

	if (!Yii::$app->user->isGuest) {

    	
    	$menuItems = [
        ['label' => 'Организация', 'url' => ['organisation/index']],
        	['label' => 'Управление ролями', 'url' => ['/permit/access/role']],
        	['label' => 'Управление правами доступа', 'url' => ['/permit/access/permission']],
        	['label' => 'Пользователи', 'url' => ['site/list']],
        	['label' => 'Статус заявок', 'url' => ['status/index']],
            ['label' => 'Категория клиентов', 'url' => ['clientcategory/index']],
            ['label' => 'Страны поставок', 'url' => ['suppliercountry/index']]
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
</div>

<div class="right-content">
	<?=$content;?>
</div>
<div class="clear"></div>

<?php $this->endContent(); ?>