<?php
use yii\helpers\Html;
use	yii\widgets\LinkPager;
/* @var $this yii\web\View */

$this->title = 'TED';
?>
<div class="site-index">

    <div class="post_block">
        <h3>Последние новости</h3>
    	<?php if(count($post)){?>
    		<ul>
    			<?php foreach ($post as $key => $p) { ?>
    				<li><?php echo Html::a($p->title, array('post/read','id'=>$p->id), array('class' => 'post_link')); ?></li>
    			<?php }?>
    		</ul>
    	<?php } ?>
    	<div class="post_pagination">
    		<?php 
    			// display pagination
				echo LinkPager::widget([
    				'pagination' => $pages,
				]);
    		?>
    	</div>
    </div>

    
</div>
