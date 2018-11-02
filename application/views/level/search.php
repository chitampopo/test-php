<?php
/**
 * Created by PhpStorm.
 * User: phuocnguyen
 * Date: 04/09/2018
 * Time: 11:43 PM
 */
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
?>
<?php
$form = ActiveForm::begin([
    'id' => 'form-search',
    'method' => 'get',
    'action' => Url::to(['/level/index']),
    'layout' => 'horizontal',
    'fieldConfig' => [
        'template' => "{label}\n{beginWrapper}\n{input}\n{hint}\n{error}\n{endWrapper}",
        'horizontalCssClasses' => [
            'label' => 'col-xs-2 col-sm-4 col-md-1 col-lg-1',
            'offset' => 'col-sm-offset-4',
            'wrapper' => 'col-xs-10 col-sm-8 col-md-11 col-lg-11'
        ],
    ],
]);
?>

<div class="col-xs-12 col-sm-6 col-md-12 col-lg-12">
    <?= $form->field($model, 'name')->textInput(['class' => 'form-control input-sm']) ?>
</div>
<div class="clearfix"></div>
<div class="col-md-12">
    <div class="form-group text-center">
        <?= Html::submitButton('<i class="fa fa-search" aria-hidden="true"></i> Tìm kiếm', ['class' => 'btn btn-sm  btn-primary']) ?>
        <?= Html::a('<i class="fa fa-plus-circle" aria-hidden="true"></i> Thêm mới', Url::to(['/level/update']), ['class' => 'btn btn-sm btn-primary']) ?>
        <?= Html::button('<i class="fa fa-trash" aria-hidden="true"></i> Xóa', ['name' => 'btnDelete', 'class' => 'btn btn-sm  btn-danger', 'onclick' => "return deleteData('level','delete')"]) ?>
    </div>
</div>
<?php ActiveForm::end(); ?>
