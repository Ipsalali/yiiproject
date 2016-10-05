<?php

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\helpers\Html;
use yii\helpers\Url;
use frontend\models\Autotruck;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use common\models\SupplierCountry;


$countries = SupplierCountry::find()->all();
$this->title = "Заявки";

$Autotrucks = Autotruck::find()->orderBy(['id'=>SORT_DESC])->all();

$query = $this->params['query'];
$filters = $this->params['filters'];
$dataProvider = new ActiveDataProvider([
    'query'=>$query,
    'pagination'=>[
        'pageSize'=>15]
    ]);
?>

<?php $this->beginContent('@app/views/layouts/main.php'); ?>
<div class="left_bar">
    <div class="left_bar_head">
        <div class="new_app"><?php echo Html::a('Добавить заявку', array('autotruck/create'), array('class' => 'btn btn-primary')); ?></div>
        <div class="filter_app">
            <form action="" method="GET" id="form-autotrucks-filter">
                <input type="hidden" name='r' value="autotruck/index">
                <select name="filters[country]" class="form-control">
                    <option value=0>Все</option>
                    <?php if(count($countries)){ foreach ($countries as $key => $c) { 
                        $selected = (array_key_exists('country', $filters) && $c->id ==$filters['country'])? 'selected' : '';
                    ?>
                   <option value="<?php echo $c->id?>" <?php echo $selected?>><?php echo $c->country?></option>
                <?php } } ?>
                </select>
            </form>
        </div>
    </div>
    <div class="autotrUcks_list">
        <?php
    echo GridView::widget([
            'dataProvider'=>$dataProvider,
            'rowOptions'=>function ($model, $key, $index, $grid) {
                    $active = ($index==0)? 'active_tab':'';
                    return ['data-id' => $model['id'],'class'=>'app_tabs '.$active];
            },
            'tableOptions' => [
                'class' => 'table'
            ],
            'columns'=>[
                    'name',
                    [
                        'attribute' => 'Дата',
                        'value' => function (Autotruck $data) {
                            return Html::a(Html::encode($data->ruDate), Url::to(['autotruck/read', 'id' => $data->id]));
                        },
                        'format' => 'raw',
                     ],
                    [
                      'attribute'=>'Страна',
                      'format'=>'html',
                      'value' => function($data){
                                    if($data->countryCode)
                                        return "<span class='badge'>".$data->countryCode."</span>";
                                    else return "-";
                                },
                    ]
            ]
        ])
        ?>
    </div>
    <?php if($data && 0){?>
        <ul class="list-group app_tabs">
            <?php $active="active_tab"; foreach ($data as $key => $app){?>
                <li class="list-group-item <?=$active?>" data-id="<?=$app->id?>">
                    <span class="badge"><?=count($app->getApps())?></span>
                    <a id="app_<?=$app->id?>" class="app_link"><?=$app->id?> от <?=date("d.m.Y",strtotime($app->date))?></a>
                </li>
            <?php $active=''; } ?>
        </ul>
        <?php //echo Html::ul($data)?>
    <?php } ?>
</div>
<div class="right_content">
	<?=$content;?>
</div>
<div class="clear"></div>

<?php $this->endContent(); ?>