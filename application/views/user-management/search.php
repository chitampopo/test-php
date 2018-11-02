<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
?>

<?php
$form = ActiveForm::begin([
    'id' => 'form-search',
    'method' => 'get',
    'action' => Url::to(['/user-management/index']),
    'layout' => 'horizontal',
    'fieldConfig' => [
        'template' => "{label}\n{beginWrapper}\n{input}\n{hint}\n{error}\n{endWrapper}",
        'horizontalCssClasses' => [
            'label' => 'col-xs-2 col-sm-4 col-md-2 col-lg-2',
            'offset' => 'col-sm-offset-4',
            'wrapper' => 'col-xs-10 col-sm-8 col-md-10 col-lg-10'
        ],
    ],
]);
?>

<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
    <?= $form->field($model, 'keyword')->textInput(['class' => 'form-control input-sm']) ?>
    <?= $form->field($model, 'level_id')->dropDownList(\application\models\Level\LevelUtil::getDropdownList(false),['class'=>'chosen-select'])->label('Chức vụ:') ?>
</div>
<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
    <?= $form->field($model, 'department_id')->dropDownList(\application\models\Department\DepartmentUtil::getDropdownList(false),['class'=>'chosen-select'])->label('Phòng ban:')?>
</div>
<div class="clearfix"></div>
<div class="col-md-12">
    <div class="form-group text-center">
        <?= Html::submitButton('<i class="fa fa-search" aria-hidden="true"></i> Tìm kiếm', ['class' => 'btn btn-sm  btn-primary']) ?>
        <?= Html::a('<i class="fa fa-plus-circle" aria-hidden="true"></i> Thêm mới', Url::to(['/user-management/update']), ['class' => 'btn btn-sm btn-primary']) ?>
        <?= Html::button('<i class="fa fa-trash" aria-hidden="true"></i> Xóa', ['name' => 'btnDelete', 'class' => 'btn btn-sm  btn-danger', 'onclick' => "return deleteData('user-management','delete')"]) ?>
    </div>
</div>
<?php ActiveForm::end(); ?>
