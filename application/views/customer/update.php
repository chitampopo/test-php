<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use common\widgets\Alert;
use yii\helpers\Url;

$this->title = 'Cập nhật khách hàng';
?>
<div class="breadcrumbs" id="breadcrumbs">
    <ul class="breadcrumb">
        <li>
            <i class="ace-icon fa fa-home home-icon"></i>
            <a href="<?= Url::to(['/']) ?>">Trang chủ</a>
        </li>
        <li>
            <a href="<?= Url::to(['/customer/index']) ?>">Danh sách khách hàng</a>
        </li>
        <li class="active">
            <a href="#">Cập nhật khách hàng</a>
        </li>
    </ul>
</div>
<br>
<div class="page-content">
    <?= Alert::widget() ?>
    <?php
    $form_params = [
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
    ];
    if (!empty($postUrl)) {
        $form_params['action'] = $postUrl;
    }
    ?>
    <?php $form = ActiveForm::begin($form_params); ?>
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                <?= $form->field($model, 'title')->textInput(['class' => 'form-control input-sm'])->label('Xưng hô'); ?>
                <?= $form->field($model, 'name')->textInput(['class' => 'form-control input-sm'])->label('Họ tên'); ?>
                <?= $form->field($model, 'phone')->textInput(['class' => 'form-control input-sm'])->label('Điện thoại'); ?>
                <?= $form->field($model, 'sex')->label('Giới tính')->dropDownList($sex, ['class' => 'form-control input-sm']); ?>
                <?= $form->field($model, 'birthday')->label('Tuổi')->textInput(['class' => 'form-control input-sm','type'=>'number']); ?>

                <?= $form->field($model, 'job_id')->dropDownList($jobs, ['class' => 'form-control input-sm'])->label('Công việc'); ?>
                <?= $form->field($model, 'salary')->textInput(['class' => 'form-control input-sm' ])->label('Mức thu nhập'); ?>
                <?= $form->field($model, 'address')->textInput(['class' => 'form-control input-sm'])->label('Địa chỉ'); ?>
                <?= $form->field($model, 'email')->label('Email')->input('email',['class' => 'form-control input-sm']); ?>
                <?= $form->field($model, 'marital_status_id')->dropDownList($maritalStatus, ['class' => 'form-control input-sm'])->label('Hôn nhân'); ?>
                <?= $form->field($model, 'number_of_children')->textInput(['class' => 'form-control input-sm', 'type' => 'number'])->label('Số con'); ?>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                <?= $form->field($model, 'chanel_id')->label('Nguồn')->dropDownList($channels, ['class' => 'form-control input-sm']); ?>
                <?php
                if($model->is_lock_change_category==1){
                  echo $form->field($model, 'category')->label('Phân loại')->dropDownList($categories, ['class' => 'form-control input-sm',"disabled"=>true]);
                }else{
                    echo $form->field($model, 'category')->label('Phân loại')->dropDownList($categories, ['class' => 'form-control input-sm']);
                }
                ?>
            </div>

        </div>
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                <div class="form-group">
                    <label class="control-label col-sm-3 col-md-3 col-lg-3"></label>
                    <div class="col-sm-8 col-md-8 col-lg-8">
                        <?= Html::submitButton('<i class="fa fa-floppy-o" aria-hidden="true"></i> Lưu thông tin', ['class' => 'btn btn-sm btn-primary', 'name' => 'btnSave']) ?>
                        <?= Html::a('<i class="fa fa-arrow-left" aria-hidden="true"></i> Thoát', Url::to([$backUrl]), ['class' => 'btn btn-sm btn-danger', 'name' => 'btnExit']) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>

