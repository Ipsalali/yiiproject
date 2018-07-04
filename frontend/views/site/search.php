<?php
use yii\helpers\Html;
use frontend\models\App;
use frontend\models\autotruck;
use common\models\Client;
?>

<?php if(is_array($sender) || is_array($apps) || is_array($autotrucks) || is_array($clients)){?>
<div class="results">

	<?php if(is_array($clients) && count($clients)){ ?>
		<div class="search-clients">
		<h4>Клиенты (<?php echo count($clients)?>)</h4>
		<ul>
			<?php foreach ($clients as $key => $cl) { 
				$client = Client::findOne($cl['id']); ?>
				<li>
					<div>
					<p class="autotruck_name">
							<?php echo Html::a(Html::encode($client->name), array('client/read', 'id'=>$client->id),array("target"=>"_blank")); ?>
						</p>
					<p>Телефон: <?php echo Html::encode($client->phone); ?></p>
					<p>Email: <?php echo Html::encode($cl['us_email']); ?></p>
					<p>Email(Доп): <?php echo Html::encode($cl['cl_email']); ?></p>
					</div>
				</li>
			<?php }?>
		</ul>
		</div>
	<?php } ?>

	<?php if(is_array($autotrucks) && count($autotrucks)){ ?>
		<div class="search-autotrucks">
		<h4>Заявки (<?php echo count($autotrucks)?>)</h4>
		<ul>
			<?php foreach ($autotrucks as $key => $a) { 
					$autotruck = Autotruck::findOne($a['id']);
				?>
				<li>
					<div>
						<p class="autotruck_name">
							<?php echo Html::a(Html::encode($autotruck->name), array('autotruck/read','id'=>$autotruck->id),array("target"=>"_blank")); ?>
						</p>
						<small>Дата: <?php echo date("d.m.y",strtotime(Html::encode($autotruck->date))); ?></small>
						<small>Статус: <?php echo Html::encode($autotruck->activeStatus->title); ?></small>
					</div>
				</li>
			<?php }?>
		</ul>
		</div>
	<?php } ?>

	<?php if(is_array($apps) && count($apps)){ ?>
		<div class="search-apps">
		<h4>Наименования (<?php echo count($apps)?>)</h4>
		<ul>
			<?php foreach ($apps as $key => $a) { 
					$app = App::findOne($a['id']);
				?>
				<li>
					<div>
						<p class="app_info">
							<?php echo Html::a(Html::encode($app->info), array('autotruck/read','id'=>$app->autotruck_id),array("target"=>"_blank")); ?>
						</p>
						<p class="app_comments"><?php echo Html::encode($app->comment); ?></p>
						<small>Вес: <?php echo Html::encode($app->weight); ?></small>
						<small>Количество: <?php echo Html::encode($app->weight); ?></small>
					</div>
				</li>
			<?php }?>
		</ul>
		</div>
	<?php } ?>

	<?php if(is_array($sender) && count($sender)){ ?>
		<div class="search-apps">
		<h4>Отправители (<?php echo count($sender)?>)</h4>
		<ul>
			<?php foreach ($sender as $key => $a) { 
					//$sender = Sender::findOne($a['id']);
				?>
				<li>
					<div>
						<p class="app_info">
							<?php echo Html::a(Html::encode($a['name']), array('sender/read','id'=>$a['id']),array("target"=>"_blank")); ?>
						</p>
						<small>Телефон: <?php echo Html::encode($a['phone']); ?></small>
						<small>E-mail: <?php echo Html::encode($a['email']); ?></small>
					</div>
				</li>
			<?php }?>
		</ul>
		</div>
	<?php } ?>

	
</div>
<?php } ?>
