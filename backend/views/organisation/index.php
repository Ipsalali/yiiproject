<?php 
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use common\models\Organisation;

$this->title = "Организация";
?>
 
<?php if(Yii::$app->session->hasFlash('StatusDeletedError')): ?>
<div class="alert alert-error">
    There was an error deleting your post!
</div>
<?php endif; ?>
 
<?php if(Yii::$app->session->hasFlash('StatusDeleted')): ?>
<div class="alert alert-success">
    Your post has successfully been deleted!
</div>
<?php endif; ?>
<div class="alert alert-success active_change" style="display: none;"></div>
<div class="org_page">
	<?php echo Html::a('Добавить организацию', array('organisation/create'), array('class' => 'btn btn-primary')); ?>

<div class="clearfix"></div>

<div class="">
    <table class="table">
        <tr>
            <th>№</th>
            <th>Наименование организации</th>
            <th>Активный</th>
            <th>Удалить</th>
        </tr>
        <?php if(count($orgs)){ ?>
            <?php foreach ($orgs as $key => $org) { 
                $checked = $org->active?'checked':'';
                ?>
                <tr>
                    <td><?php echo $key+1; ?></td>
                    <td><?php echo Html::a($org->org_name,array('organisation/read','id'=>$org->id)); ?></td>
                    <td>
                        <?php $form = ActiveForm::begin(['action'=>['organisation/setactive',['id'=>$org->id]]]); ?>
                            <input type="radio" name="active_org" class="toActiveOrg" id="toActiveOrg<?php echo $org->id?>" value="<?php echo $org->id;?>" <?php echo $checked;?>>
                        <?php ActiveForm::end();?>
                    </td>
                    <td>
                        <?php $form = ActiveForm::begin(['action'=>['organisation/remove']]); ?>
                            <input type="hidden" name="org_id" value="<?php echo $org->id;?>">
                            <input type="submit" name="remove_org" class="remove_org" value="Удалить">
                        <?php ActiveForm::end();?>
                    </td>
                </tr>
            <?php } ?>
        <?php } ?>
    </table>
</div>
</div>