<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use common\widgets\Alert;
use yii\helpers\Url;

$this->title = 'Báo cáo trình bày FHC';
?>
<div class="breadcrumbs" id="breadcrumbs">
    <ul class="breadcrumb">
        <li>
            <i class="ace-icon fa fa-home home-icon"></i>
            <a href="<?= Url::to(['/']) ?>">Trang chủ</a>
        </li>
        <li>
            <a href="<?= Url::to(['/fhc-report/index']) ?>">Báo cáo trình bày FHC</a>
        </li>
        <li class="active">
            <a href="#">Cập nhật báo cáo trình bày FHC</a>
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
                'label' => 'col-sm-4 col-md-4 col-lg-4',
                'offset' => 'col-sm-offset-4',
                'wrapper' => 'col-sm-8 col-md-8 col-lg-8'
            ],
        ],
    ];
    if (!empty($postUrl)) {
        $form_params['action'] = $postUrl;

    }
    ?>
    <?php $form = ActiveForm::begin($form_params);
            $url_quick_add_customer = '/customer/create?go-back=true&ctl=fhc-report&action=update';
    ?>
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                <?=
                $form->field($model, 'customer_id',
                    ['inputTemplate' => '<div class="input-group">{input}<span class="input-group-btn"><a class="btn btn-primary btn-sm btn-quick-add"  href="'.Url::to([$url_quick_add_customer]).'">Thêm</a></span></div>'])
                    ->dropDownList($customers, ['class' => 'form-control input-sm chosen-select']) ?>
                <?= $form->field($model, 'date')->textInput(['class' => 'form-control input-sm date-picker']) ?>
                <?= $form->field($model, 'demand')->dropDownList($demands,['class'=>'chosen-select','multiple'=>true])->label('Nhu cầu'); ?>

                <div class="form-group">
                    <label class="control-label col-sm-4 col-md-4 col-lg-4"></label>
                    <div class="col-sm-8 col-md-8 col-lg-8">
                        <input type="hidden" name="FhcReport[sis]" value="0">
                        <label><input type="checkbox" <?= ($model->sis == 1) ? 'checked' : '' ?> id="fhc-sis" name="FhcReport[sis]" value="1"> SIS</label>
                    </div>
                </div>

                <?= $form->field($model, 'khtn')->textInput(['class' => 'form-control input-sm', 'type' => 'number'])->label('KHTN'); ?>
                <div class="form-group">
                    <label class="control-label col-sm-4 col-md-4 col-lg-4"></label>
                    <div class="col-sm-8 col-md-8 col-lg-8">
                        <input type="hidden" name="FhcReport[jfw]" value="0">
                        <label><input type="checkbox" <?= ($model->jfw == 1) ? 'checked' : '' ?> id="fhc-sis" name="FhcReport[jfw]" value="1"> JFW</label>
                    </div>
                </div>
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

