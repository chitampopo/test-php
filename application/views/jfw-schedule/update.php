<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\helpers\Html;
use common\widgets\Alert;
use application\utilities\DatetimeUtils;
$this->title = "Cập nhật kế hoạch";
?>
<div class="breadcrumbs" id="breadcrumbs">
    <ul class="breadcrumb">
        <li>
            <i class="ace-icon fa fa-home home-icon"></i>
            <a href="<?= Url::to(['/']) ?>">Trang chủ</a>
        </li>
        <li>
            <a href="<?= Url::to(['/jfw-schedule/index']) ?>">Kế hoạch nhân viên</a>
        </li>
        <li class="active">
            <a href="#">Cập nhật</a>
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
    $url_quick_add_customer = '/customer/create?go-back=true&ctl=jfw-schedule&action=update';
    if(!empty($model->id)){
        $url_quick_add_customer.="&id=".$model->id;
    }
    ?>
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-9 col-lg-9">
                <?= $form->field($model, 'customer_id',
                    ['inputTemplate' => '<div class="input-group">{input}<span class="input-group-btn"><a class="btn btn-primary btn-sm btn-quick-add"  href="'.Url::to([$url_quick_add_customer]).'">Thêm</a></span></div>'])
                    ->dropDownList($customers, ['class' => 'form-control input-sm chosen-select']) ?>
                <div class="form-group">
                    <label class="control-label col-xs-12 col-sm-2 col-md-2 col-lg-2">Ngày thực hiện</label>
                    <div class="col-xs-5 col-sm-2 col-md-2 col-lg-2">
                        <input type="text" class="form-control input-sm date-picker" name="PersonalSchedule[date]" id="jfwschedule-date" value="<?= $model->date ?>">
                    </div>
                    <div class="col-xs-3 col-sm-2 col-md-2 col-lg-2">
                        <?= DatetimeUtils::buildInputHour('PersonalSchedule',$model->hour) ?>
                    </div>
                    <div class="col-xs-4 col-sm-2 col-md-2 col-lg-2">
                        <?= DatetimeUtils::buildInputMinute('PersonalSchedule',$model->minute) ?>
                    </div>
                </div>

                <?= $form->field($model, 'chanel_id')->dropDownList($chanels, ['class' => 'form-control input-sm']) ?>
                <?= $form->field($model, 'purpose_id')->dropDownList($purposes, ['class' => 'form-control input-sm']) ?>
                <div class="form-group">
                    <label class="control-label col-sm-2 col-md-2 col-lg-2"></label>
                    <div class="col-sm-8 col-md-8 col-lg-8">
                        <input type="hidden" name="PersonalSchedule[is_new_customer]" value="0">
                        <label><input type="checkbox" <?= ($model->is_new_customer == 1) ? 'checked' : '' ?> id="jfwschedule-is_new_customer" name="CallResult[is_new_customer]" value="1"> Khách hàng mới</label>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-2 col-md-2 col-lg-2"></label>
                    <div class="col-sm-8 col-md-8 col-lg-8">
                        <input type="hidden" name="PersonalSchedule[is_call]" value="0">
                        <label><input type="checkbox" <?= ($model->is_call == 1) ? 'checked' : '' ?>
                                      id="jfwschedule-is_call" name="PersonalSchedule[is_call]" value="1"> Cuộc gọi ?</label>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-2 col-md-2 col-lg-2"></label>
                    <div class="col-sm-8 col-md-8 col-lg-8">
                        <input type="hidden" name="PersonalSchedule[completed]" value="0">
                        <label><input type="checkbox" onchange="toggleComplete(this)" <?= ($model->completed == 1) ? 'checked' : '' ?>
                                      id="jfwschedule-completed" name="PersonalSchedule[completed]" value="1"> Hoàn thành ?</label>
                    </div>
                </div>
                <?= $form->field($model, 'completed_date')->textInput(['class' => 'form-control input-sm date-picker']) ?>
                <div class="form-group">
                    <label class="control-label col-sm-2 col-md-2 col-lg-2"></label>
                    <div class="col-sm-8 col-md-8 col-lg-8">
                        <?= Html::submitButton('<i class="fa fa-floppy-o" aria-hidden="true"></i> Lưu thông tin', ['class' => 'btn btn-sm btn-primary', 'name' => 'btnSave']) ?>
                        <?= Html::a('<i class="fa fa-arrow-left" aria-hidden="true"></i> Thoát', Url::to(['/jfw-schedule']), ['class' => 'btn btn-sm btn-danger', 'name' => 'btnExit']) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
    
    <?php $this->registerJs('
        function isScheduleCompletedChecked (attribute, value) {
            return $("#jfwschedule-completed").prop("checked") ? true : false;
        };
        jQuery( "#jfwschedule-completed" ).change(function() {
            $("#form-update-post").yiiActiveForm("validateAttribute", "completed_date");
        });
        jQuery( "#jfwschedule-completed" ).keyup(function() {
            $("#form-update-post").yiiActiveForm("validateAttribute", "completed_date");
        });'); 
    ?>

</div>

<script type="text/javascript">
    function toggleComplete(source) {
        var complete = source.checked;
        console.log(complete);
        if (complete == false) {
            $('#jfwschedule-completed_date').val("");
        } else {
            $('#jfwschedule-completed_date').val($.datepicker.formatDate( "dd/mm/yyyy", new Date()));
        }
        return false;
    }
</script>


