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
