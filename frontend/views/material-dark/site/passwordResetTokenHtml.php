<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $user common\models\User */

$resetLink = Yii::$app->urlManager->createAbsoluteUrl(['site/reset-password', 'token' => $user->password_reset_token]);
?>
<!DOCTYPE html>
<html>
<head>
	<title>Восстановление пароля</title>
	<meta charset="utf-8">
</head>
<body>
<div class="password-reset">
    <p>Здравствуйте.</p>

    <p>Перейдите по ссылке для восстановления вашего пароля:</p>

    <p><?php echo Html::a(Html::encode($resetLink), $resetLink) ?></p>
</div>
</body>
</html>