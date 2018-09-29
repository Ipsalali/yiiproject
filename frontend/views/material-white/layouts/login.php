<?php

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use frontend\assets\AppAsset;
use frontend\assets\MaterialWhiteAsset;
// use common\widgets\Alert;
use frontend\bootstrap4\AlertJS as Alert;
use yii\filters\AccessControl;

MaterialWhiteAsset::register($this);
?>
<?php $this->beginPage(); ?>
<!DOCTYPE html>
<html lang="<?php echo Yii::$app->language; ?>">
<head>
    <link rel="apple-touch-icon" sizes="76x76" href="templates/material-white/img/apple-icon.png">
    <link rel="icon" type="image/png" href="templates/material-white/img/favicon.png">
    <meta charset="<?php echo Yii::$app->charset; ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php echo Html::csrfMetaTags(); ?>
    <title><?php echo Html::encode($this->title); ?></title>
    <?php $this->head(); ?>
</head>
<body>
<?php $this->beginBody(); ?>
    <div class="container">
        <?php echo Alert::widget(); ?>
        <?php echo $content; ?>
    </div>
<?php $this->endBody(); ?>
</body>
</html>
<?php $this->endPage(); ?>
