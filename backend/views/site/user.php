<?php

use yii\helpers\Html;

$posts = $user->posts;


?>

<div class="user-info">
	<p>Логин: <span><?php echo $user->username;?></span></p>
	<p>ФИО: <span><?php echo $user->name;?></span></p>
	<p>Телефон: <?php echo $user->phone?></p>
	<p>E-mail: <span><?php echo $user->email;?></span></p>
	<p>Права: <?php echo $user->role->description?></p>
	<div>
		Добавленные новости (<?php echo count($posts)?>):
		<ul>
			<?php
				foreach ($posts as $k => $p) {?>
					<li><?php echo $p->title; ?></li>
				<?php }
			?>
		</ul>
	</div>
</div>