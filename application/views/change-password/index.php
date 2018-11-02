<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use common\widgets\Alert;
use yii\helpers\Url;

$this->title = 'Thay đổi mật khẩu';
?>

<div class="breadcrumbs" id="breadcrumbs">
    <ul class="breadcrumb">
        <li>
            <i class="ace-icon fa fa-home home-icon"></i>
            <a href="<?= Url::to(['/']) ?>">Trang chủ</a>
        </li>

        <li class="active">
            <a href="#">Thay đổi mật khẩu</a>
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
                'label' => 'col-sm-3 col-md-3 col-lg-3',
                'offset' => 'col-sm-offset-4',
                'wrapper' => 'col-sm-9 col-md-9 col-lg-9'
            ],
        ],
    ]);
    ?>
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-9 col-lg-9">
                <?= $form->field($model, 'currentPassword')->passwordInput(['class' => 'form-control input-sm']) ?>
                <?= $form->field($model, 'newPassword')->passwordInput(['class' => 'form-control input-sm']) ?>
                <?= $form->field($model, 'repeatNewPassword')->passwordInput(['class' => 'form-control input-sm']) ?>
                <div class="form-group">
                    <label class="control-label col-sm-3 col-md-3 col-lg-3"></label>
                    <div class="col-sm-8 col-md-8 col-lg-8">
                        <?= Html::submitButton('<i class="fa fa-floppy-o" aria-hidden="true"></i> Lưu thông tin', ['class' => 'btn btn-sm btn-primary', 'name' => 'btnSave']) ?>
                        <?= Html::a('<i class="fa fa-arrow-left" aria-hidden="true"></i> Thoát', Url::to(['/']), ['class' => 'btn btn-sm btn-danger', 'name' => 'btnExit']) ?>
                    </div>
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">

            </div>
        </div>
    </div>
<?php ActiveForm::end(); ?>
</div>