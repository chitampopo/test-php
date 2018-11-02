<?php
/**
 * Created by PhpStorm.
 * User: phuocnguyen
 * Date: 04/09/2018
 * Time: 11:52 PM
 */

use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\helpers\Html;
use common\widgets\Alert;
use application\utilities\DatetimeUtils;
use application\utilities\DetectDeviceUtil;
use application\utilities\PermissionUtil;
$this->title = "Cập nhật cuộc gọi";
?>
<div class="breadcrumbs" id="breadcrumbs">
    <ul class="breadcrumb">
        <li>
            <i class="ace-icon fa fa-home home-icon"></i>
            <a href="<?= Url::to(['/']) ?>">Trang chủ</a>
        </li>
        <li>
            <a href="<?= Url::to(['/call-result/index']) ?>">Cuộc gọi</a>
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
    $url_quick_add_customer = '/customer/create?go-back=true&ctl=call-result&action=update';
    if (!empty($model->id)) {
        $url_quick_add_customer .= "&id=" . $model->id;
    }
    ?>
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-9 col-lg-9">
                <?php
                    if(\application\utilities\DetectDeviceUtil::isDesktop()) {
                        echo $form->field($model, 'customer_id',
                            ['inputTemplate' => '<div class="input-group">{input}<span class="input-group-btn"><a class="btn btn-primary btn-sm btn-quick-add"  href="' . Url::to([$url_quick_add_customer]) . '">Thêm</a></span></div>'])
                            ->dropDownList($customers, ['class' => 'form-control input-sm chosen-select', 'onchange' => 'fillChanelFromCustomer(this.value)']);
                    } else {
                        echo $form->field($model, 'customer_id',
                           ['inputTemplate' => '<div class="input-group">{input}<span class="input-group-btn"><a class="btn btn-primary btn-sm btn-quick-add"  href="' . Url::to([$url_quick_add_customer]) . '">Thêm</a></span></div>'])
                            ->dropDownList($customers, ['class' => 'form-control input-sm chosen-select', 'id' => 'combobox', 'onchange' => 'fillChanelFromCustomer(this.value)']);
                    }

                ?>
                <?= $form->field($model, 'chanel_id')->dropDownList($chanels, ['class' => 'form-control input-sm']) ?>
                <?= $form->field($model, 'purpose_id')->dropDownList($purposes, ['class' => 'form-control input-sm']) ?>
                <?php
                if(DetectDeviceUtil::isMobile()){
                    echo $form->field($model, 'call_date')->textInput(['class' => 'form-control input-sm','type'=>'date']);
                }else {
                    echo $form->field($model, 'call_date')->textInput(['class' => 'form-control input-sm date-picker']);
                }?>
                <div class="form-group">
                    <label class="control-label hidden-xs col-sm-2 col-md-2 col-lg-2"></label>
                    <div class="col-sm-8 col-md-8 col-lg-8">
                        <input type="hidden" name="CallResult[is_new_call]" value="0">
                        <label><input type="checkbox" <?= ($model->is_new_call == 1) ? 'checked' : '' ?> id="callresult-is_new_call" name="CallResult[is_new_call]" value="1"> Cuộc gọi mới</label>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label hidden-xs col-sm-2 col-md-2 col-lg-2"></label>
                    <div class="col-sm-8 col-md-8 col-lg-8">
                        <input type="hidden" name="CallResult[result]" value="0">
                        <label><input type="checkbox" <?= ($model->result == 1) ? 'checked' : '' ?> id="callresult-result" name="CallResult[result]" value="1"> Kết quả</label>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-xs-12 col-sm-2 col-md-2 col-lg-2">Ngày hẹn</label>
                    <div class="col-xs-5 col-sm-2 col-md-2 col-lg-2">
                        <?php
                        if(DetectDeviceUtil::isMobile()){?>
                            <input type="date" class="form-control input-sm" name="CallResult[appointment_date]" id="callresult-appointment_date" value="<?= $model->appointment_date ?>">
                        <?php } else { ?>
                            <input type="text" class="form-control input-sm date-picker" name="CallResult[appointment_date]" id="callresult-appointment_date" value="<?= $model->appointment_date ?>">
                        <?php } ?>

                    </div>
                    <div class="col-xs-3 col-sm-2 col-md-2 col-lg-2">
                        <?= DatetimeUtils::buildInputHour('CallResult',$model->hour) ?>
                    </div>
                    <div class="col-xs-4 col-sm-2 col-md-2 col-lg-2">
                        <?= DatetimeUtils::buildInputMinute('CallResult',$model->minute) ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label hidden-xs col-sm-2 col-md-2 col-lg-2"></label>
                    <div class="col-sm-8 col-md-8 col-lg-8">
                        <input type="hidden" name="CallResult[is_add_schedule]" value="0">
                        <label><input type="checkbox" <?= ($model->is_add_schedule == 1) ? 'checked' : '' ?> id="callresult-is_add_schedule" name="CallResult[is_add_schedule]" value="1"> Thêm vào lịch hẹn</label>
                    </div>
                </div>
                <?= $form->field($model, 'note')->textarea(['class' => 'form-control input-sm']) ?>
                <?= $form->field($model, 'schedule_id')->hiddenInput()->label(false) ?>
                <div class="form-group">
                    <label class="control-label col-sm-2 col-md-2 col-lg-2"></label>
                    <div class="col-sm-8 col-md-8 col-lg-8">
                        <?= Html::submitButton('<i class="fa fa-floppy-o" aria-hidden="true"></i> Lưu thông tin', ['class' => 'btn btn-sm btn-primary', 'name' => 'btnSave']); ?>
                        <?= Html::a('<i class="fa fa-arrow-left" aria-hidden="true"></i> Thoát', Url::to([$backUrl]), ['class' => 'btn btn-sm btn-danger', 'name' => 'btnExit']) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>

<script type="text/javascript">
    function fillChanelFromCustomer(customer_id) {
        $.ajax({
            url: '<?=Url::to(['/customer/get-customer-info'])?>',
            data: {
                customer_id: customer_id
            },
            type: "POST",
            success: function (data) {
                var obj = JSON.parse(data);
                $("#callresult-chanel_id").val(obj.chanel_id);
                $("#callresult-chanel_id").focus();
            },
            error: function () {
            }
        });
        return false;
    }
</script>


