<?php
use yii\helpers\Html; 
use yii\widgets\ActiveForm;
use common\models\ClientCategory;
use common\models\User;
use yii\grid\GridView;
use yii\data\ActiveDataProvider;
use yii\helpers\Arrayhelper;
use yii\helpers\Url;



$client_cats = ClientCategory::find()->all();
$managers = User::getManagers();

?>
 
<?php if(Yii::$app->session->hasFlash('ClientDeletedError')): ?>
<div class="alert alert-error">
    There was an error deleting your post!
</div>
<?php endif; ?>
 
<?php if(Yii::$app->session->hasFlash('ClientDeleted')): ?>
<div class="alert alert-success">
    Your post has successfully been deleted!
</div>
<?php endif; ?>

<?php echo Html::a('Добавить клиента', array('client/create'), array('class' => 'btn btn-primary pull-right')); ?>
<div class="clearfix"></div>



<?php 

// Формируем форму для поиска по safe аттрибутам
// if (($safeAttributes = $filterModel->safeAttributes())) {
//     echo Html::beginTag('div', ['class' => 'well']);
//     $form = ActiveForm::begin([
//             'method' => $requestType
//         ]);
//     foreach ($safeAttributes as $attribute) {
//         echo $form->field($filterModel, $attribute)->dropDownList(ArrayHelper::map(ClientCategory::find()->all(),'cc_id','cc_title'),['prompt'=>'Выберите категорию'])->label("Категория");
//     }
//     echo Html::submitInput('Фильтр', ['class' => 'btn btn-default']).
//         Html::endTag('div');
//     ActiveForm::end();
// }

// echo \yii\grid\GridView::widget([
//         'dataProvider' => $dataProvider,
//         'filterModel' => $filterModel,
//         'columns'=>[
//             ['class'=>'yii\grid\SerialColumn'],
//             'name',
//             'email:email',
//             'phone',
//             'categoryTitle',
//             ['class' => 'yii\grid\ActionColumn',
//          'template' => '{view}&nbsp;&nbsp;{update}&nbsp;&nbsp;{delete}',
//          'buttons' =>
//              [
//                  'view' => function ($url, $model) {
//                      return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', Url::to(['/client/read', 'id' => $model->id]), [
//                          'title' => Yii::t('yii', 'view')
//                      ]); },
//                  'update' => function ($url, $model) {
//                      return Html::a('<span class="glyphicon glyphicon-pencil"></span>', Url::to(['/client/update', 'id' => $model->id]), [
//                          'title' => Yii::t('yii', 'update')
//                      ]); },   
//                  'delete' => function ($url, $model) {
//                      return Html::a('<span class="glyphicon glyphicon-trash"></span>', Url::to(['/client/delete', 'id' => $model->id]), [
//                          'title' => Yii::t('yii', 'Change user role')
//                      ]); },
//              ]
//         ],
//             'managerName'
//         ]
// ]);

?>

<form action="<?php echo Url::to(["client/index"]);?>" method="GET" id="form-client-filters">
<table class="table table-striped table-hover">
    <tr>
        <td>#</td>
        <td class="td_client_name">Название</td>
        <td>E-mail</td>
        <td>Телефон</td>
        <td class="td_client_category">Категория</td>
        <td class="td_client_manager">Ответственный</td>
         <td>Действия</td>
    </tr>
    <tr><td></td><td></td><td></td><td></td>
        <td>
            <select name="filters[client_category_id]" id="client_category_id" class="form-control">
                <option disabled>Выберите категорию</option>
                <option value="0">Вcе</option>
                <?php if(count($client_cats)){ foreach ($client_cats as $key => $cc) { 
                        $selected = (array_key_exists('client_category_id', $filters) && $cc->cc_id ==$filters['client_category_id'])? 'selected' : '';
                    ?>
                   <option value="<?php echo $cc->cc_id?>" <?php echo $selected?>><?php echo $cc->cc_title?></option>
                <?php } } ?>
            </select>
        </td>
        <td>
            <select name="filters[manager]" id="manager" class="form-control">
                <option disabled>Выберите категорию</option>
                <option value="0">Вcе</option>
                <?php print_r($managers); if(count($managers)){ foreach ($managers as $key => $m) { 
                    $selected = (array_key_exists('manager', $filters) && $m->id ==$filters['manager'])? 'selected' : '';
                    ?>
                   <option value="<?php echo $m->id?>" <?php echo $selected?>><?php echo $m->username?></option>
                <?php } } ?>
            </select>
        </td>
        <td></td>
    </tr>
    <?php foreach ($data as $client): ?>
        <tr>
            <td>
                <?php echo Html::a($client->id, array('client/read', 'id'=>$client->id)); ?>
            </td>
            <td><?php echo Html::a($client->name, array('client/read', 'id'=>$client->id)); ?></td>
            <td><?php echo $client->user->email; ?></td>
            <td><?php echo $client->phone; ?></td>
            <td><?php echo $client->category->cc_title;?></td>
            <td><?php echo $client->managerUser->username;?></td>
            <td>
                <?php echo Html::a("<span class='glyphicon glyphicon-pencil'></span>", array('client/update', 'id'=>$client->id), array('class'=>'icon icon-edit')); ?>
                <?php echo Html::a("X", array('client/delete', 'id'=>$client->id), array('class'=>'btn btn-sm btn-danger icon-trash remove_check')); ?>
            </td>
        </tr>
    <?php endforeach; ?>
</table>
<input type="hidden" name="r" value="client/index">
</form>