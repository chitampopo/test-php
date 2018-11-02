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
    'action' => Url::to(['/fhc-report/index']),
    'layout' => 'horizontal',
    'fieldConfig' => [
        'template' => "{label}\n{beginWrapper}\n{input}\n{hint}\n{error}\n{endWrapper}",
        'horizontalCssClasses' => [
            'label' => 'col-xs-4 col-sm-4 col-md-3 col-lg-3',
            'offset' => 'col-sm-offset-4',
            'wrapper' => 'col-xs-8 col-sm-8 col-md-9 col-lg-9'
        ],
    ],
]);
?>

<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
    <?= $form->field($model, 'customer_id')->label('Tên/SĐT:')->textInput(['class' => 'form-control input-sm']) ?>
    <?= (PermissionUtil::isHodRole() || PermissionUtil::isAdminRole() || PermissionUtil::isXCRole()) ? $form->field($model, 'department_id')->label('Phòng:')->dropDownList(DepartmentUtil::getDropdownList(false),['onchange'=>'loadNhanVien(this.value)']) : "" ?>
    <?php
    if(!PermissionUtil::isXPRole()){
        echo $form->field($model, 'user_id')->dropDownList($users,['class' => 'form-control input-sm chosen-select'])->label('Nhân viên:');
    }
    ?>
</div>
<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
    <?php
    if (DetectDeviceUtil::isMobile()) {
        echo $form->field($model, 'from_date')->textInput(['class' => 'form-control input-sm', 'type' => 'date'])->label('Từ ngày');
    } else {
        echo $form->field($model, 'from_date')->textInput(['class' => 'form-control input-sm date-picker'])->label('Từ ngày');
    }
    if (DetectDeviceUtil::isMobile()) {
        echo $form->field($model, 'to_date')->textInput(['class' => 'form-control input-sm','type'=>'date'])->label('Đến ngày');
    }else{
        echo $form->field($model, 'to_date')->textInput(['class' => 'form-control input-sm date-picker'])->label('Đến ngày');
    }
    ?>


</div>
<div class="clearfix"></div>
<div class="col-md-12">
    <div class="form-group text-center">
        <?= Html::submitButton('<i class="fa fa-search" aria-hidden="true"></i> Tìm kiếm', ['class' => 'btn btn-sm  btn-primary']) ?>
        <?= Html::button('<i class="fa fa-file-excel-o" aria-hidden="true"></i> Xuất Excel', ['class' => 'btn btn-sm  btn-primary','onclick'=>'exportExcel()']) ?>
    </div>
</div>
<?php ActiveForm::end(); ?>
<script type="text/javascript">
    function loadNhanVien(department_id) {
        $.ajax({
            url: '<?=Url::to(['/user-management/get-nhan-viens'])?>',
            data: {
                department_id: department_id,
                is_load_leader:1,
                is_not_required:1
            },
            type: "POST",
            beforeSend: function (xhr) {
                showLoading();
            },
            success: function (data) {
                $("#fhcreportsearch-user_id").html(data);
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
    function exportExcel() {
        var from_date = $("#fhcreportsearch-from_date").val();
        var to_date = $("#fhcreportsearch-to_date").val();
        var customer_id = $("#fhcreportsearch-customer_id").val();
        var department_id = $("#fhcreportsearch-department_id").val();
        var user_id = $("#fhcreportsearch-user_id").val();

        $.ajax({
            url: '<?=Url::to(['/fhc-report/export-excel'])?>',
            data: {
                from_date: from_date,
                to_date: to_date,
                customer_id: customer_id,
                department_id:department_id,
                user_id:user_id
            },
            type: "POST",
            beforeSend: function (xhr) {
                showLoading();
            },
            success: function (data) {
                if (data !== "") {
                    window.location.href = data;
                }
                hideLoading();
            },
            error: function () {
                alert("Lỗi hệ thống. Bạn không thể xuất dữ liệu");
                hideLoading();
            }
        });
        return false;
    }
</script>