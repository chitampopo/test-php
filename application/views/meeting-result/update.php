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
$this->title = "Cập nhật cuộc gặp";
?>

<div class="breadcrumbs" id="breadcrumbs">
    <ul class="breadcrumb">
        <li>
            <i class="ace-icon fa fa-home home-icon"></i>
            <a href="<?= Url::to(['/']) ?>">Trang chủ</a>
        </li>
        <li>
            <a href="<?= Url::to(['/meeting-result/index']) ?>">Danh sách cuộc gặp</a>
        </li>
        <li class="active">
            <a href="#">Cập nhật cuộc gặp</a>
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
    $url_quick_add_customer = '/customer/create?go-back=true&ctl=meeting-result&action=update';
    if(!empty($model->id)){
        $url_quick_add_customer.="&id=".$model->id;
    }
    ?>
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-9 col-lg-9">
                <?php
                if(DetectDeviceUtil::isDesktop()) {
                    echo $form->field($model, 'customer_id',
                        ['inputTemplate' => '<div class="input-group">{input}<span class="input-group-btn"><a class="btn btn-primary btn-sm btn-quick-add"  href="' . Url::to([$url_quick_add_customer]) . '">Thêm</a></span></div>'])
                        ->dropDownList($customers, ['class' => 'form-control input-sm chosen-select', 'onchange' => 'fillChanelFromCustomer(this.value)']);
                } else {
                    echo $form->field($model, 'customer_id',
                       ['inputTemplate' => '<div class="input-group">{input}<span class="input-group-btn"><a class="btn btn-primary btn-sm btn-quick-add"  href="' . Url::to([$url_quick_add_customer]) . '">Thêm</a></span></div>'])
                        ->dropDownList($customers, ['class' => 'form-control input-sm chosen-select col-9', 'id' => 'combobox', 'onchange' => 'fillChanelFromCustomer(this.value)']);
                }
                ?>
                <?= $form->field($model, 'chanel_id')->dropDownList($chanels, ['class' => 'form-control input-sm']) ?>
                <?php
                if(DetectDeviceUtil::isMobile()){
                    echo $form->field($model, 'meeting_date')->textInput(['class' => 'form-control input-sm','type'=>'date']);
                } else{
                    echo $form->field($model, 'meeting_date')->textInput(['class' => 'form-control input-sm date-picker']);
                }
                ?>

                <div class="form-group">
                    <label class="control-label col-sm-3"></label>
                    <div class="col-sm-9">
                        <div class="widget-box">
                            <!--<div class="widget-header"><h4 class="smaller">Thông tin cuộc gặp</h4></div>-->
                            <div class="widget-body">
                                <div class="widget-main">
                                        <input type="hidden" name="MeetingResult[is_new_meeting]" value="0">
                                        <label class="col-xs-6 col-sm-3 col-md-3 col-lg-3"><input type="checkbox" <?= ($model->is_new_meeting == 1) ? 'checked' : '' ?> id="meetingresult-is_new_meeting" name="MeetingResult[is_new_meeting]" value="1"> Gặp mới</label>

                                        <input type="hidden" name="MeetingResult[hd]" value="0">
                                        <label class="col-xs-6 col-sm-3 col-md-3 col-lg-3"><input type="checkbox" <?= ($model->hd == 1) ? 'checked' : '' ?> id="meetingresult-hd" name="MeetingResult[hd]" value="1"> HĐ</label>

                                        <input type="hidden" name="MeetingResult[sis]" value="0">
                                        <label class="col-xs-6 col-sm-3 col-md-3 col-lg-3"><input type="checkbox" <?= ($model->sis == 1) ? 'checked' : '' ?> id="meetingresult-sis" name="MeetingResult[sis]" value="1"> SIS</label>

                                        <input type="hidden" name="MeetingResult[warm]" value="0">
                                        <label class="col-xs-6 col-sm-3 col-md-3 col-lg-3"><input type="checkbox" <?= ($model->warm == 1) ? 'checked' : '' ?> id="meetingresult-warm" name="MeetingResult[warm]" value="1"> WARM UP</label>
                                        <input type="text" class="form-control" name="MeetingResult[other]" placeholder="Lý do khác" value="<?= $model->other ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-sm-3 col-md-3 col-lg-3"></label>
                        <div class="col-sm-9">
                            <div class="widget-box">
                                <div class="widget-header">
                                    <h4 class="widget-title">
                                        <input type="hidden" name="MeetingResult[fhc]" value="0">
                                        <label><input type="checkbox" <?= ($model->fhc == 1) ? 'checked' : '' ?> id="meetingresult-fhc" name="MeetingResult[fhc]" value="<?= $model->fhc ?>"> Đã thực hiện FHC</label>
                                    </h4>
                                </div>

                                <div class="widget-body">
                                    <div class="widget-main">
                                        <input type="hidden" name="FhcReport[id]" value="<?= $model->fhc_report->id ?>">
                                        <?= $form->field($model->fhc_report, 'demand')->dropDownList($demands,['class'=> 'form-control input-sm chosen-select', 'multiple'=>true])->label('Nhu cầu'); ?>
                                        <?= $form->field($model->fhc_report, 'khtn')->label('KHTN FHC')->textInput(['type' => 'number','class'=>'form-control input-sm']); ?>
                                        <?= $form->field($model->fhc_report, 'salary')->label('Mức lương')->textInput(['class'=>'form-control input-sm']); ?>
                                        <?= $form->field($model->fhc_report, 'job_id')->label('Nghề nghiệp')->dropDownList($jobs,['class'=>'form-control input-sm']); ?>
                                    </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?= $form->field($model, 'khtn')->label('KHTN')->textInput(['type' => 'number']); ?>
                <?= $form->field($model, 'reject_reason')->label('Lí do từ chối')->textInput(); ?>
                <div class="form-group">
                    <label class="control-label col-xs-12 col-sm-3 col-md-3 col-lg-3">Ngày hẹn</label>
                    <div class="col-xs-5 col-sm-2 col-md-2 col-lg-2">
                        <?php
                        if(DetectDeviceUtil::isMobile()){?>
                            <input type="date" class="form-control input-sm" name="MeetingResult[follow_up_date]" id="meetingresult-follow_up_date" value="<?= $model->follow_up_date ?>">
                        <?php }else{?>
                            <input type="text" class="form-control input-sm date-picker" name="MeetingResult[follow_up_date]" id="meetingresult-follow_up_date" value="<?= $model->follow_up_date ?>">
                        <?php } ?>
                    </div>
                    <div class="col-xs-3 col-sm-2 col-md-2 col-lg-2">
                        <?= DatetimeUtils::buildInputHour('MeetingResult',$model->hour) ?>
                    </div>
                    <div class="col-xs-4 col-sm-2 col-md-2 col-lg-2">
                        <?= DatetimeUtils::buildInputMinute('MeetingResult',$model->minute) ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label hidden-xs col-sm-3 col-md-3 col-lg-3"></label>
                    <div class="col-sm-9 col-md-9 col-lg-9">
                        <input type="hidden" name="MeetingResult[is_add_schedule]" value="0">
                        <label><input type="checkbox" <?= ($model->is_add_schedule == 1) ? 'checked' : '' ?> id="meetingresult-is_add_schedule" name="MeetingResult[is_add_schedule]" value="1"> Thêm vào lịch hẹn</label>
                    </div>
                </div>
                <?= $form->field($model, 'note')->label('Ghi chú')->textarea(['class'=>'form-control input-sm']); ?>
                <?= $form->field($model, 'schedule_id')->hiddenInput()->label(false) ?>
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
                $("#meetingresult-chanel_id").val(obj.chanel_id);
                $("#meetingresult-chanel_id").focus();
            },
            error: function () {
            }
        });
        return false;
    }
</script>

