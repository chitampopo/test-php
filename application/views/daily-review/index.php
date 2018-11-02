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
use application\utilities\PermissionUtil;
use application\utilities\DetectDeviceUtil;
$this->title = "Daily review";
?>
<div class="breadcrumbs" id="breadcrumbs">
    <ul class="breadcrumb">
        <li>
            <i class="ace-icon fa fa-home home-icon"></i>
            <a href="<?= Url::to(['/']) ?>">Trang chủ</a>
        </li>
        <li class="active">
            <a href="#">Daily review</a>
        </li>
    </ul>
</div>
<br>
<div class="page-content">
    <?php
    $form = ActiveForm::begin([
        'id' => 'form-update-post',
        'layout' => 'horizontal',
        'fieldConfig' => [
            'template' => "{label}\n{beginWrapper}\n{input}\n{hint}\n{error}\n{endWrapper}",
            'horizontalCssClasses' => [
                'label' => 'col-xs-2 col-sm-2 col-md-2 col-lg-2',
                'offset' => 'col-sm-offset-4',
                'wrapper' => 'col-xs-10 col-sm-10 col-md-10 col-lg-10'
            ],
        ],
    ]);
    ?>
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-9 col-lg-9">
                <?php
                if(DetectDeviceUtil::isMobile()){
                    echo $form->field($model, 'date')->textInput(['class' => 'form-control input-sm','type'=>'date']);
                }else{
                    echo $form->field($model, 'date')->textInput(['class' => 'form-control input-sm date-picker']);
                }
                if(!PermissionUtil::isXPRole()) {
                    echo $form->field($model, 'department_id')->dropDownList($departments, ['class' => 'form-control input-sm chosen-select', 'onchange' => 'loadNhanVien(this.value)']);
                    echo $form->field($model, 'user_id')->dropDownList($users, ['class' => 'form-control input-sm chosen-select']);
                }
                ?>
                <div class="form-group">
                    <label class="control-label col-xs-2 col-sm-2 col-md-2 col-lg-2"></label>
                    <div class="col-xs-10 col-sm-10 col-md-10 col-lg-10">
                        <?= Html::button('<i class="fa fa-file-excel-o" aria-hidden="true"></i> Xem báo cáo', ['class' => 'btn btn-sm btn-primary','onclick'=>'exportExcel()']) ?>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>


<script type="text/javascript">
    function loadNhanVien(department_id) {
        $.ajax({
            url: '<?=Url::to(['/user-management/get-nhan-viens'])?>',
            data: {
                department_id: department_id,
                is_load_leader: 0,
                is_not_required: 1
            },
            type: "POST",
            beforeSend: function (xhr) {
                showLoading();
            },
            success: function (data) {
                $("#dailyreview-user_id").html(data);
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
        var date = $("#dailyreview-date").val();
        var user = $("#dailyreview-user_id").val();
        var department = $("#dailyreview-department_id").val();

        $.ajax({
            url: '<?=Url::to(['/daily-review/export-excel'])?>',
            data: {
                date: date,
                user: user,
                department:department
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

