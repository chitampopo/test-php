<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use application\utilities\PermissionUtil;
use application\models\Department\DepartmentUtil;
use application\utilities\DetectDeviceUtil;
?>
<?php
$form = ActiveForm::begin([
    'id' => 'form-search',
    'method' => 'get',
    'action' => Url::to(['/jfw-schedule/index']),
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
    <?= $form->field($jfwScheduleSearch, 'customer_id')->textInput(['class' => 'form-control input-sm']) ?>
    <?= $form->field($jfwScheduleSearch, 'completed')->dropDownList($completedStatus, ['class' => 'form-control input-sm']) ?>

</div>
<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
    <?php
    if(DetectDeviceUtil::isMobile()) {
        echo $form->field($jfwScheduleSearch, 'date')->textInput(['class' => 'form-control input-sm','type'=>'date']);
    }else{
        echo $form->field($jfwScheduleSearch, 'date')->textInput(['class' => 'form-control input-sm date-picker']);
    }
    ?>
    <?= PermissionUtil::isHodRole() || PermissionUtil::isAdminRole() ? $form->field($jfwScheduleSearch, 'department_id')->label('Phòng:')->dropDownList(DepartmentUtil::getDropdownList(false),['onchange'=>'loadNhanVien(this.value)']) : "" ?>
    <?= !PermissionUtil::isXPRole() ? $form->field($jfwScheduleSearch, 'user_id')->dropDownList($users, ['class' => 'form-control input-sm chosen-select']) : ""?>
</div>
<div class="clearfix"></div>
<div class="col-md-12">
    <div class="form-group text-center">
        <?= Html::submitButton('<i class="fa fa-search" aria-hidden="true"></i> Tìm kiếm', ['class' => 'btn btn-sm  btn-primary']) ?>
    </div>
</div>
<?php ActiveForm::end(); ?>

<script type="text/javascript">
    function loadNhanVien(department_id) {
        $.ajax({
            url: '<?=Url::to(['/user-management/get-nhan-viens'])?>',
            data: {
                department_id: department_id,
                is_not_required:1,
                is_load_leader:1
            },
            type: "POST",
            beforeSend: function (xhr) {
                showLoading();
            },
            success: function (data) {
                $("#jfwschedulesearch-user_id").html(data);
                $(".chosen-select").trigger("chosen:updated");
                hideLoading();
            },
            error: function () {
                alert("Lỗi hệ thống. Bạn không thể load dữ liệu");
                hideLoading();
            }
        });
        return false;
    }   
</script>
