<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use application\utilities\DetectDeviceUtil;
?>
<?php
$form = ActiveForm::begin([
    'id' => 'form-search',
    'method' => 'get',
    'action' => Url::to(['/personal-schedule/index']),
    'layout' => 'horizontal',
    'fieldConfig' => [
        'template' => "{label}\n{beginWrapper}\n{input}\n{hint}\n{error}\n{endWrapper}",
        'horizontalCssClasses' => [
            'label' => 'col-xs-3 col-sm-4 col-md-3 col-lg-3',
            'offset' => 'col-sm-offset-4',
            'wrapper' => 'col-xs-9 col-sm-8 col-md-9 col-lg-9'
        ],
    ],
]);
?>

<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
    <?= $form->field($personalScheduleSearch, 'customer_id')->textInput(['class' => 'form-control input-sm']) ?>
    <?= $form->field($personalScheduleSearch, 'completed')->dropDownList($completedStatus, ['class' => 'form-control input-sm']) ?>
</div>
<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
    <?php
    if(DetectDeviceUtil::isMobile()) {
        echo $form->field($personalScheduleSearch, 'date')->textInput(['class' => 'form-control input-sm','type'=>'date']);
    }else{
        echo $form->field($personalScheduleSearch, 'date')->textInput(['class' => 'form-control input-sm date-picker']);
    }
    ?>
</div>
<div class="clearfix"></div>
<div class="col-md-12">
    <div class="form-group text-center">
        <?= Html::submitButton('<i class="fa fa-search" aria-hidden="true"></i> Tìm kiếm', ['class' => 'btn btn-sm  btn-primary']) ?>
        <?= Html::a('<i class="fa fa-plus-circle" aria-hidden="true"></i> Thêm mới', Url::to(['/personal-schedule/update']), ['class' => 'btn btn-sm btn-primary']) ?>
        <?= Html::button('<i class="fa fa-trash" aria-hidden="true"></i> Xóa', ['name' => 'btnDelete', 'class' => 'btn btn-sm  btn-danger', 'onclick' => "return deleteData('personal-schedule','delete')"]) ?>
    </div>
</div>
<?php ActiveForm::end(); ?>
