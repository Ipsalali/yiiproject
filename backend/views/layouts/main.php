<?php

/* @var $this \yii\web\View */
/* @var $content string */

use backend\assets\AppAsset;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use common\widgets\Alert;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
    <?php
    NavBar::begin([
        'brandLabel' => 'TED',
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar-inverse navbar-fixed-top',
        ],
    ]);
    $menuItems = [
        ['label' => 'Главная', 'url' => ['/site/index']],
        ['label' => 'Настройки', 'url' => ['/setting/index']],
        ['label' => 'Пользователи', 'url' => ['/site/list']],
    ];
    if (Yii::$app->user->isGuest) {
        $menuItems[] = ['label' => 'Login', 'url' => ['/site/login']];
    } else {
        $menuItems[] = [
            'label' => 'Logout (' . Yii::$app->user->identity->username . ')',
            'url' => ['/site/logout'],
            'linkOptions' => ['data-method' => 'post']
        ];
    }
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
        'items' => $menuItems,
    ]);
    NavBar::end();
    ?>

    <div class="main">
        <div class="left_bar">
        <?php

            if (!Yii::$app->user->isGuest) {

                $menuItems = [
                ['label' => 'Организация', 'url' => ['/organisation/index']],
                ['label' => 'Управление заявками', 'url' => ['/autotruck/index']],
                ['label' => 'Управление ролями', 'url' => ['/permit/access/role']],
                ['label' => 'Управление правами доступа', 'url' => ['/permit/access/permission']],
                ['label' => 'Пользователи', 'url' => ['/site/list']],
                ['label' => 'Статус заявок', 'url' => ['/status/index']],
                ['label' => 'Категория клиентов', 'url' => ['/clientcategory/index']],
                ['label' => 'Страны поставок', 'url' => ['/suppliercountry/index']],
                ['label' => 'Статус оплаты', 'url' => ['/paymentstate/index']],
                // ['label' => 'Виды оплат', 'url' => ['/payment/index']],
                ['label' => 'Тип упаковки', 'url' => ['/typepackaging/index']],
                ['label' => 'Отправители', 'url' => ['/sender/index']],
                ];
                    echo Nav::widget([
                        'options' => ['class' => 'nav nav-list'],
                        'items' => $menuItems,
            ]);
            } else {
               
            }
            
    
        ?>
        </div>
        
        <div class="right-content">
            <?= Breadcrumbs::widget([
                'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
             ]) ?>

        <?= Alert::widget() ?>
            <?=$content;?>
        </div>
        <div class="clear"></div>
    </div>
</div>

<footer class="footer">
    <div class="container">
        <p class="pull-left">&copy; TED <?= date('Y') ?></p>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
