<?php

/* @var $this \yii\web\View */
/* @var $content string */

use backend\assets\AppAsset;
use yii\helpers\{Html,Url};
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use common\widgets\Alert;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?php echo Yii::$app->language ?>">
<head>

    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    
    <?php echo Html::csrfMetaTags() ?>
    
    <title><?php echo Html::encode($this->title) ?></title>

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    <?php $this->head() ?>
</head>

<body>
<?php $this->beginBody() ?>
    <div id="wrapper">

        <!-- Navigation -->
        <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
            <div class="navbar-header">
                
                <a class="navbar-brand" href="<?php echo Url::to(['/site/index']);?>"><?php echo Html::encode("TED CRM Панель управления") ?></a>
            </div>
            <!-- /.navbar-header -->

            <ul class="nav navbar-top-links navbar-right">
                
                <!-- /.dropdown -->
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        <i class="fa fa-user fa-fw"></i> <i class="fa fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-user">
                        
                        <?php if(Yii::$app->hasModule("profiler")){ ?>
                            <li>
                                <?php echo Html::a('<i class="fa fa-user fa-fw"></i> Мой профиль',['/profiler/user/profile'])?>
                            </li>
                            <li>
                                <?php echo Html::a('<i class="fa fa-gear fa-fw"></i> Настройки профиля',['/profiler/user/settings'])?>
                            </li>
                        <?php } ?>
                        <li class="divider"></li>
                        <li>
                            <?php 
                               echo Html::beginForm(['/site/logout'], 'post')
                                    . Html::submitButton(
                                            '<i class="fa fa-sign-out fa-fw"></i> Logout',
                                            ['class' => 'btn btn-link logout']
                                    )
                                    . Html::endForm()
                            ?>
                            <!-- <a href="login.html"> Logout</a> -->
                        </li>
                    </ul>
                    <!-- /.dropdown-user -->
                </li>
                <!-- /.dropdown -->
            </ul>
            <!-- /.navbar-top-links -->

            <?php
                echo $this->render("left_menu",[]);
            ?>
            
            <!-- /.navbar-static-side -->
        </nav>

        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header"><?php echo $this->title;?></h1>
                </div>
            </div>
            
            <?php  echo Breadcrumbs::widget([
                            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                    ]) 
            ?>

            <?php  echo Alert::widget(); ?>
            
            <?php echo $content; ?>
        </div>
        <!-- /#page-wrapper -->

    </div>
    <!-- /#wrapper -->

    
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
