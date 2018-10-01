<?php

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use frontend\bootstrap4\Breadcrumbs;
use frontend\assets\AppAsset;
use frontend\assets\MaterialDarkAsset;
use frontend\bootstrap4\AlertJS as Alert;
use yii\filters\AccessControl;

MaterialDarkAsset::register($this);
?>
<?php $this->beginPage(); ?>
<!DOCTYPE html>
<html lang="<?php echo Yii::$app->language; ?>">
<head>
    <link rel="apple-touch-icon" sizes="76x76" href="template/material/img/apple-icon.png">
    <link rel="icon" type="image/png" href="template/material/img/favicon.png">
    <meta charset="<?php echo Yii::$app->charset; ?>">
    
    <?php echo Html::csrfMetaTags(); ?>
    <title><?php echo Html::encode($this->title); ?></title>
    <?php $this->head(); ?>
</head>
<body class="dark-edition">
<?php $this->beginBody(); ?>

<div class="wrapper">
    
    <?php echo $this->render("sidebar-menu",[]);?>
    
    <div class="main-panel">
      <!-- Navbar -->
      <nav class="navbar navbar-expand-lg navbar-transparent navbar-absolute fixed-top ">
        <div class="container-fluid">
          
          <!-- <button class="navbar-toggler" type="button" data-toggle="collapse" aria-controls="navigation-index" aria-expanded="false" aria-label="Toggle navigation">
            <span class="sr-only">Toggle navigation</span>
            <span class="navbar-toggler-icon icon-bar"></span>
            <span class="navbar-toggler-icon icon-bar"></span>
            <span class="navbar-toggler-icon icon-bar"></span>
          </button> -->
          <div class="collapse navbar-collapse justify-content-end">
            <?php if(!Yii::$app->user->isGuest && !Yii::$app->user->identity->isClient() && !Yii::$app->user->identity->isSeller(true)){?>
                  <?php echo $this->render("module-search",[]);?>
            <?php } ?>

            <?php if (!Yii::$app->user->isGuest) { ?>
                  <ul class="navbar-nav">
                      <li class="nav-item dropdown">
                      <a class="nav-link" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="material-icons">person</i>
                        <p class="d-lg-none d-md-block">
                          Профиль
                        </p>
                      </a>
                      <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownMenuLink">
                       <?php echo Html::a("Выход",['/site/logout'],['data-method'=>'post','class'=>'dropdown-item'])?>
                      </div>
                    </li>
                  
                  </ul>
            <?php } ?>
          </div>
        </div>
      </nav>
      <!-- End Navbar -->
      
      <div class="content">
        <div class="container-fluid">
          <div class="row">
            <div class="col-12">
                <?php 
                  echo  Breadcrumbs::widget(['links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],]); 
                ?>
                <?php echo Alert::widget(); ?>
            </div>
          </div>
          <?php echo $content; ?>
        </div>
      </div>

      <footer class="footer">
        <div class="container-fluid">
          <div class="copyright float-right">
            &copy; 2016, made with <i class="material-icons">favorite</i> 
            <!-- by <a href="https://www.web-ali.ru" target="_blank">Ali team.</a> -->
          </div>
        </div>
      </footer>
    </div>
</div>

<script type="text/javascript">$(function(){
    isWindows = navigator.platform.indexOf('Win') > -1 ? true : false;

  if (isWindows) {
    // if we are on windows OS we activate the perfectScrollbar function
    $('.sidebar .sidebar-wrapper, .main-panel').perfectScrollbar();

    $('html').addClass('perfect-scrollbar-on');
  } else {
    $('html').addClass('perfect-scrollbar-off');
  }
})</script>
<?php $this->endBody(); ?>
</body>
</html>
<?php $this->endPage(); ?>
