<?php

use yii\helpers\Url;
use yii\grid\GridView;
use yii\rbac\Item;
use yii\helpers\Html;
use backend\modules\rbac\RbacAsset;

$this->title = 'Управление ролями и правами доступа';
$this->params['breadcrumbs'][] = $this->title;

RbacAsset::register($this);
?>
<div class="row">
    <div class="col-xs-6">
        <div class="panel panel-info role-info-table">
            <div class="panel-heading"><?= Yii::t('rbac', 'Роли')?></div>
            <div class="panel-body">
                <?= GridView::widget([
                    'dataProvider' => $rolesSearchModel->search(Yii::$app->request->queryParams),
                    'filterModel' => $rolesSearchModel,
                    'columns' => [
                        'name:text:Название',
                        'description:text:Описание',
                        [
                            'class' => 'yii\grid\ActionColumn',
                            'template' => '{update} {delete} {children}',
                            'buttons' => [
                                'update' => function($url, $model, $key) {
                                    $options = [
                                        'title' => Yii::t('yii', 'Update'),
                                        'aria-label' => Yii::t('yii', 'Update'),
                                        'data-pjax' => '0',
                                    ];
                                    return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url . '&type=' . $model->type, $options);
                                },
                                'children' => function($url, $model, $key) {
                                    $options = [
                                        'title' => Yii::t('rbac', 'CHILDREN'),
                                        'aria-label' => Yii::t('rbac', 'CHILDREN'),
                                        'data-pjax' => '0',
                                    ];
                                    return Html::a('<span class="glyphicon glyphicon-link"></span>', $url . '&type=' . $model->type, $options);
                                },
                                'delete' => function($url, $model, $key) {
                                    $options = [
                                        'title' => Yii::t('yii', 'Delete'),
                                        'aria-label' => Yii::t('yii', 'Delete'),
                                        'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                                        'data-method' => 'post',
                                        'data-pjax' => '0',
                                    ];
                                    return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url . '&type=' . $model->type, $options);
                                }
                            ]
                        ],
                    ],
                ]); ?>
            </div>
            <div class="panel-footer">
                <a class="btn btn-success" href="<?= Url::toRoute(['rbac/create', 'type' => Item::TYPE_ROLE])?>" role="button"><?= Yii::t('rbac', 'CREATE')?></a>
            </div>
        </div>
    </div>

    <div class="col-xs-6">
        <div class="panel panel-info">
            <div class="panel-heading"><?= Yii::t('rbac', 'Права')?></div>
            <div class="panel-body">
                <?= GridView::widget([
                    'dataProvider' => $permissionsSearchModel->search(Yii::$app->request->queryParams),
                    'filterModel' => $permissionsSearchModel,
                    'columns' => [
                        'name:text:Название',
                        'description:text:Описание',
                        [
                            'class' => 'yii\grid\ActionColumn',
                            'template' => '{update} {delete} {children}',
                            'buttons' => [
                                'update' => function($url, $model, $key) {
                                    $options = [
                                        'title' => Yii::t('yii', 'Update'),
                                        'aria-label' => Yii::t('yii', 'Update'),
                                        'data-pjax' => '0',
                                    ];
                                    return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url . '&type=' . $model->type, $options);
                                },
                                'children' => function($url, $model, $key) {
                                    $options = [
                                        'title' => Yii::t('rbac', 'CHILDREN'),
                                        'aria-label' => Yii::t('rbac', 'CHILDREN'),
                                        'data-pjax' => '0',
                                    ];
                                    return Html::a('<span class="glyphicon glyphicon-link"></span>', $url . '&type=' . $model->type, $options);
                                },
                                'delete' => function($url, $model, $key) {
                                    $options = [
                                        'title' => Yii::t('yii', 'Delete'),
                                        'aria-label' => Yii::t('yii', 'Delete'),
                                        'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                                        'data-method' => 'post',
                                        'data-pjax' => '0',
                                    ];
                                    return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url . '&type=' . $model->type, $options);
                                }
                            ]
                        ],
                    ],
                ]); ?>
            </div>
            <div class="panel-footer">
                <a class="btn btn-success" href="<?= Url::toRoute(['rbac/create', 'type' => Item::TYPE_PERMISSION])?>" role="button"><?= Yii::t('rbac', 'CREATE')?></a>
            </div>
        </div>
        
    </div>

    <div class="col-xs-12">
        <div class="panel panel-info">
            <div class="panel-heading"><?= Yii::t('rbac', 'Правила')?></div>
            <div class="panel-body">
                <?= GridView::widget([
                    'dataProvider' => $rulesSearchModel->search(Yii::$app->request->queryParams),
                    'filterModel' => $rulesSearchModel,
                    'columns' => [
                        'name',
                        [
                            'header' => Yii::t('rbac', 'PHP_CLASS'),
                            'value' => function($data) {
                                return unserialize($data->data)->className();
                            }
                        ],
                        [
                            'class' => 'yii\grid\ActionColumn',
                            'template' => '{delete}',
                        ],
                    ],
                ]); ?>
            </div>
            <div class="panel-footer">
                <a class="btn btn-success" href="<?= Url::toRoute(['rbac/create'])?>" role="button"><?= Yii::t('rbac', 'CREATE')?></a>
            </div>
        </div>
    </div>
</div>