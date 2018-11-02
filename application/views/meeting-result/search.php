<?php
/**
 * Created by PhpStorm.
 * User: phuocnguyen
 * Date: 04/09/2018
 * Time: 11:43 PM
 */
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use application\utilities\PermissionUtil;
use application\utilities\DetectDeviceUtil;
use application\models\Department\DepartmentUtil;
?>
<?php
$form = ActiveForm::begin([
    'id' => 'form-search',
    'method' => 'get',
    'action' => Url::to(['/meeting-result/index']),
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
    <?= $form->field($meetingResultSearch, 'customer_id')->textInput(['class' => 'form-control input-sm']) ?>
    <?= (PermissionUtil::isHodRole() || PermissionUtil::isAdminRole() || PermissionUtil::isXCRole()) ? $form->field($meetingResultSearch, 'department_id')->label('Phòng:')->dropDownList(DepartmentUtil::getDropdownList(false),['class'=>'form-control input-sm','onchange'=>'loadNhanVien(this.value)']) : "" ?>
</div>
<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
    <?php
    if(DetectDeviceUtil::isMobile()) {
        echo $form->field($meetingResultSearch, 'from_date')->textInput(['class' => 'form-control input-sm','type'=>'date']);
        echo $form->field($meetingResultSearch, 'meeting_date')->textInput(['class' => 'form-control input-sm','type'=>'date']);
    } else{
        echo $form->field($meetingResultSearch, 'from_date')->textInput(['class' => 'form-control input-sm date-picker']);
        echo $form->field($meetingResultSearch, 'meeting_date')->textInput(['class' => 'form-control input-sm date-picker']);
    }
    if(!PermissionUtil::isXPRole()) {
        echo $form->field($meetingResultSearch, 'user_id')->dropDownList($users, ['class' => 'form-control input-sm chosen-select']);
    }
    ?>
</div>
<div class="clearfix"></div>
<div class="col-md-12">
    <div class="form-group text-center">
        <?= Html::submitButton('<i class="fa fa-search" aria-hidden="true"></i> Tìm kiếm', ['class' => 'btn btn-sm  btn-primary']) ?>
        <?= Html::a('<i class="fa fa-plus-circle" aria-hidden="true"></i> Thêm mới', Url::to(['/meeting-result/update']), ['class' => 'btn btn-sm btn-primary']) ?>
        <?= Html::button('<i class="fa fa-trash" aria-hidden="true"></i> Xóa', ['name' => 'btnDelete', 'class' => 'btn btn-sm  btn-danger', 'onclick' => "return deleteData('meeting-result','delete')"]) ?>
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
                $("#meetingresultsearch-user_id").html(data);
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