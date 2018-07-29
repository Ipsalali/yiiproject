<?php

use yii\helpers\Html;

?>

<div class="row">
	<div class="col-xs-6">
		<p>Логин: <span><?php echo $user->username;?></span></p>
		<p>ФИО: <span><?php echo $user->name;?></span></p>
		<p>Телефон: <?php echo $user->phone?></p>
		<p>E-mail: <span><?php echo $user->email;?></span></p>
		<p>Права: <?php echo $user->role->description?></p>
		
		<div class="row">
			<div class="col-xs-12">
				<h4>Журнал изменений:</h4>
				<?php
					$stories = $user->getHistory();

					if(count($stories)){
						?>

						<table class="table table-bordered table-collapsed">
							<tr>
								<th>№</th>
								<th>Логин</th>
								<th>ФИО</th>
								<th>Телефон</th>
								<th>E-mail</th>
								<th>Версия</th>
								<th>Действие</th>
								<th>Автор действия</th>
								<th>Время действия</th>
							</tr>
							<?php
								foreach ($stories as $key => $s) {
									?>
									<tr>
										<td><?php echo $key++?></td>
										<td><?php echo $s['username'];?></td>
										<td><?php echo $s['name']?></td>
										<td><?php echo $s['phone']?></td>
										<td><?php echo $s['email']?></td>
										<td><?php echo $s['version']?></td>
										<td>
											<?php 
												$type = $s['type_action'] == 1 && $s['version'] > 1 ? 2 : $s['type_action'];
												
												echo $user->getStoryAction($type);
											?>
										</td>
										<td><?php echo $s['creator_name']," #",$s['creator_id']?></td>
										<td><?php echo $s['created_at']?></td>
									</tr>
									<?php
								}
							?>
						</table>

						<?php
					}
				?>
			</div>
		</div>

	</div>
</div>
