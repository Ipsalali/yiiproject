<?php

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use frontend\assets\AppAsset;
use common\widgets\Alert;
use yii\filters\AccessControl;

AppAsset::register($this);
?>
<?php $this->beginPage(); ?>
<!DOCTYPE html>
<html lang="<?php echo Yii::$app->language; ?>">
<head>
    <meta charset="<?php echo Yii::$app->charset; ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php echo Html::csrfMetaTags(); ?>
    <title><?php echo Html::encode($this->title); ?></title>
    <?php $this->head(); ?>
</head>
<body>
<?php $this->beginBody(); ?>

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

            if(Yii::$app->user->can("autotruck/report"))
                $menuItems[] = [
                                    'label' => 'Отчеты',
                                    'items'=>[
                                        [
                                            'label'=>'Отчет',
                                            'url' => ['/autotruck/report'],
                                        ],
                                        [
                                            'label'=>'Отчет по организациям',
                                            'url'=>["/site/org-report"]
                                        ]
                                    ]
                                ];

            if(Yii::$app->user->can("site/sverka"))
                $menuItems[] = ['label' => 'Сверка', 'url' => ['/sverka/index']];

            if(Yii::$app->user->can("client/index"))
                $menuItems[] = ['label' => 'Рассылка', 'url' => ['/spender/index']];

            if(Yii::$app->user->can("sender"))
                $menuItems[] = ['label' => 'Отправители', 'url' => ['/sender/index']];


            if(Yii::$app->user->can("transferspackage"))
                $menuItems[] = ['label' => 'Переводы', 'url' => ['/transferspackage/index']];

            
            if(Yii::$app->user->can("sellers"))
                $menuItems[] = ['label' => 'Поставщики', 'url' => ['/sellers/index']];

            
            if(Yii::$app->user->can("site/sverka"))
                $menuItems[] = ['label' => 'Организации', 'url' => ['/organisation/index']];

            
            if(Yii::$app->user->can("import/index"))
                $menuItems[] = ['label' => 'Импорт', 'url' => ['/import/index']];

            
            
        }elseif(Yii::$app->user->identity->role->name == "client"){

            if(Yii::$app->user->can("site/sverka"))
                $menuItems[] = ['label' => 'Сверка', 'url' => ['/sverka/index']];

            if(Yii::$app->user->can("Autotruck/create"))
                $menuItems[] = ['label' => 'Создать заявку', 'url' => ['/autotruck/form']];

            $menuItems[] = ['label' => 'Профиль', 'url' => ['/client/profile']];

        }
    }
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-left'],
        'items' => $menuItems,
    ]);
    NavBar::end();
    ?>
    <?php if(!Yii::$app->user->isGuest && !Yii::$app->user->identity->isClient() && !Yii::$app->user->identity->isSeller(true)){?>
    <div class="search_block">
        <div>
            <form action="" method="POST">
                <input type="text" id="search" name="search" class="form-control" placeholder="Поиск" />
            </form>
            <div class="search_result">
                <div class="results" style="display: block;">
                 </div>
            </div>
        </div>
    </div>
    <?php } ?>
        <?php if (!Yii::$app->user->isGuest) { ?>
            <div class="logout_block">
                <?php echo Html::a("Выход",['/site/logout'],['data-method'=>'post'])?>
            </div>
        <?php } ?>
    </div>
    <div class="main">
        <?php echo  Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]); ?>
        <?php echo Alert::widget(); ?>
        <?php echo $content; ?>
    </div>
</div>


<div class="clear"></div>


<?php $this->endBody(); ?>
</body>
</html>
<?php $this->endPage(); ?>
