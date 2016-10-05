<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use frontend\assets\AppAsset;
use common\widgets\Alert;
use yii\filters\AccessControl;

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
    <div class="top">
    <?php
    NavBar::begin([
        'brandLabel' => 'TED',
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar-inverse navbar-fixed-top',
        ],
    ]);
    $menuItems = [
        
    ];
    if (Yii::$app->user->isGuest) {
        //$menuItems[] = ['label' => 'Регистрация', 'url' => ['/site/signup']];
        $menuItems[] = ['label' => 'Вход', 'url' => ['/site/login']];
    } else {
        if(Yii::$app->user->identity->role->name != "client"){

            if(Yii::$app->user->can("autotruck/index"))
                $menuItems[] = ['label' => 'Заявки',  'url' => ['/autotruck/index']];

            if(Yii::$app->user->can("client/index"))
                $menuItems[] = ['label' => 'Клиенты', 'url' => ['/client/index']];

        }elseif(Yii::$app->user->identity->role->name == "client"){
            $menuItems[] = ['label' => 'Профиль', 'url' => ['/client/profile']];
        }
        $menuItems[] = [
            'label' => 'Выход',
            'url' => ['/site/logout'],
            'linkOptions' => ['data-method' => 'post']
        ];
    }
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-left'],
        'items' => $menuItems,
    ]);
    NavBar::end();
    ?>
    <?php if(!Yii::$app->user->isGuest && Yii::$app->user->identity->role->name != "client"){?>
    <div class="search_block">
        <div>
            <form action="" method="POST">
                <input type="text" id="search" name="search" placeholder="Поиск" />
                <button type="submit">Поиск</button><br>
                <small>пример: 'вася пупкин','79853421233'</small>
            </form>
            <div class="search_result">
                <div class="results" style="display: block;">
                 </div>
            </div>
        </div>
    </div>
    <?php } ?>
    </div>
    <div class="main">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
</div>


<div class="clear"></div>
<footer class="footer">
    
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
