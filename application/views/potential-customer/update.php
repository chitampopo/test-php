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
$this->title = "Cập nhật khách hàng tiềm năng";
?>
<div class="breadcrumbs" id="breadcrumbs">
    <ul class="breadcrumb">
        <li>
            <i class="ace-icon fa fa-home home-icon"></i>
            <a href="<?= Url::to(['/']) ?>">Trang chủ</a>
        </li>
        <li>
            <a href="<?= Url::to(['/potential-customer/index']) ?>">Khách hàng tiềm năng</a>
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
    $url_quick_add_customer = '/customer/create?go-back=true&ctl=potential-customer&action=update';
    if(!empty($model->id)){
        $url_quick_add_customer.="&id=".$model->id;
    }
    ?>
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-9 col-lg-9">
                <?= $form->field($customer, 'name')->textInput(['class' => 'form-control input-sm'])->label('Tên') ?>
                <?= $form->field($customer, 'phone')->textInput(['class' => 'form-control input-sm'])->label('Điện thoại') ?>
                <?= $form->field($model, 'chanel_id')->dropDownList($chanels, ['class' => 'form-control input-sm']) ?>
                <?= $form->field($model, 'customer_referral_id')->dropDownList($customers, ['class' => 'form-control input-sm chosen-select']) ?>

                <?php
                if(DetectDeviceUtil::isMobile()) {
                    echo $form->field($model, 'date')->textInput(['class' => 'form-control input-sm','type'=>'date']);
                }else{
                    echo $form->field($model, 'date')->textInput(['class' => 'form-control input-sm date-picker']);
                }
                ?>
                <div class="form-group">
                    <label class="control-label col-xs-12 col-sm-2 col-md-2 col-lg-2">Ngày gọi</label>
                    <div class="col-xs-5 col-sm-2 col-md-2 col-lg-2">
                        <?php
                        if (DetectDeviceUtil::isMobile()) { ?>
                            <input type="date" class="form-control input-sm"
                                   name="PotentialCustomer[scheduled_meeting_date]"
                                   id="potentialcustomer-scheduled_meeting_date"
                                   value="<?= $model->scheduled_meeting_date ?>">
                        <?php
                        } else {
                            ?>
                            <input type="text" class="form-control input-sm date-picker"
                                   name="PotentialCustomer[scheduled_meeting_date]"
                                   id="potentialcustomer-scheduled_meeting_date"
                                   value="<?= $model->scheduled_meeting_date ?>">
                        <?php
                        }
                        ?>

                    </div>
                    <div class="col-xs-3 col-sm-2 col-md-2 col-lg-2">
                        <?= DatetimeUtils::buildInputHour('PotentialCustomer',$model->hour) ?>
                    </div>
                    <div class="col-xs-4 col-sm-2 col-md-2 col-lg-2">
                        <?= DatetimeUtils::buildInputMinute('PotentialCustomer',$model->minute) ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-2 col-md-2 col-lg-2"></label>
                    <div class="col-sm-8 col-md-8 col-lg-8">
                        <input type="hidden" name="PotentialCustomer[is_add_schedule]" value="0">
                        <label><input type="checkbox" <?= ($model->is_add_schedule == 1) ? 'checked' : '' ?>  id="potentialcustomer-is_add_schedule" name="PotentialCustomer[is_add_schedule]" value="1"> Đặt lịch hẹn</label>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-2 col-md-2 col-lg-2"></label>
                    <div class="col-sm-8 col-md-8 col-lg-8">
                        <?= Html::submitButton('<i class="fa fa-floppy-o" aria-hidden="true"></i> Lưu thông tin', ['class' => 'btn btn-sm btn-primary', 'name' => 'btnSave']) ?>
                        <?= Html::a('<i class="fa fa-arrow-left" aria-hidden="true"></i> Thoát', Url::to(['/potential-customer']), ['class' => 'btn btn-sm btn-danger', 'name' => 'btnExit']) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>


