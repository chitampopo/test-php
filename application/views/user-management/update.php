<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\helpers\Html;
use common\widgets\Alert;

$this->title = "Cập nhật người dùng";
?>
<div class="breadcrumbs" id="breadcrumbs">
    <ul class="breadcrumb">
        <li>
            <i class="ace-icon fa fa-home home-icon"></i>
            <a href="<?= Url::to(['/']) ?>">Trang chủ</a>
        </li>
        <li>
            <a href="<?= Url::to(['/user-management/index']) ?>">Danh sách người dùng</a>
        </li>
        <li class="active">
            <a href="#">Cập nhật người dùng</a>
        </li>
    </ul>
</div>
<br>
<div class="page-content">
    <?= Alert::widget() ?>
    <?php
    $form = ActiveForm::begin([
        'id' => 'form-update-post',
        'layout' => 'horizontal',
        'fieldConfig' => [
            'template' => "{label}\n{beginWrapper}\n{input}\n{hint}\n{error}\n{endWrapper}",
            'horizontalCssClasses' => [
                'label' => 'col-sm-2 col-md-2 col-lg-2',
                'offset' => 'col-sm-offset-4',
                'wrapper' => 'col-sm-10 col-md-10 col-lg-10'
            ],
        ],
    ]);
    ?>
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-9 col-lg-9">
                <?= $form->field($model, 'name')->textInput(['class' => 'form-control input-sm']) ?>
                <?= $form->field($model, 'username')->textInput(['class' => 'form-control input-sm']) ?>
                <?= $form->field($model, 'phone')->textInput(['class' => 'form-control input-sm']) ?>
                <?= $form->field($model, 'email')->textInput(['class' => 'form-control input-sm']) ?>
                <?= $form->field($model, 'address')->textarea(['class' => 'form-control input-sm']) ?>
                <?= $form->field($model, 'level_id')->dropDownList($levels, ['class' => 'form-control input-sm']) ?>
                <?= $form->field($model, 'department_id')->dropDownList($departments, ['class' => 'form-control input-sm']) ?>
                <div class="form-group">
                    <label class="control-label col-sm-2 col-md-2 col-lg-2"></label>
                    <div class="col-sm-8 col-md-8 col-lg-8">
                        <?= Html::submitButton('<i class="fa fa-floppy-o" aria-hidden="true"></i> Lưu thông tin', ['class' => 'btn btn-sm btn-primary', 'name' => 'btnSave']) ?>
                        <?= Html::a('<i class="fa fa-arrow-left" aria-hidden="true"></i> Thoát', \yii\helpers\Url::to(['/user-management']), ['class' => 'btn btn-sm btn-danger', 'name' => 'btnExit']) ?>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>
</div>