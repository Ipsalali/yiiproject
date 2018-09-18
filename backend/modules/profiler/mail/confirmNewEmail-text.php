<?php

/* @var $this yii\web\View */
/* @var $user common\models\User */

$confirmLink = Yii::$app->urlManager->createAbsoluteUrl(['profiler/user/confirm-email', 'token' => $user->email_token]);
?>
Hello <?= $user->fullName ?>,

Follow the link below to confirm your email:

<?= $confirmLink ?>
