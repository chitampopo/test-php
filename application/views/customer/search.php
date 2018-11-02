<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use application\utilities\PermissionUtil;
use application\models\User\UserUtil;
use application\models\Chanel\ChanelUtil;
use application\models\Department\DepartmentUtil;
use application\utilities\DetectDeviceUtil;
?>
<?php
$form = ActiveForm::begin([
    'id' => 'form-search',
    'method' => 'get',
    'action' => Url::to(['/customer/index']),
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

<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
    <?= $form->field($model, 'name')->textInput(['class' => 'form-control input-sm'])->label('Tên/SĐT:') ?>
    <?= $form->field($model, 'categories')->dropDownList($categories, ['class' => 'form-control input-sm'])->label('Nhóm') ?>
    <?php
    if (!PermissionUtil::isXPRole()) {
        echo $form->field($model, 'department_id')->label('Phòng:')->dropDownList(DepartmentUtil::getDropdownList(false), ['class' => 'form-control input-sm', 'onchange' => "loadNhanVien(this.value, 'customersearch-user_id')"]);
    }
    ?>
</div>
<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
    <?= $form->field($model, 'chanel_id')->label('Kênh:')->dropDownList(ChanelUtil::getDropdownList(false), ['class' => 'form-control input-sm']) ?>
    <?php
    if (!PermissionUtil::isXPRole()) {
        echo $form->field($model, 'user_id')->label('Nhân viên:')->dropDownList($users, ['class' => 'form-control input-sm chosen-select']);
    }
    ?>
</div>
<div class="clearfix"></div>
<div class="col-md-12">
    <div class="form-group text-center">
        <?= Html::submitButton('<i class="fa fa-search" aria-hidden="true"></i> Tìm kiếm', ['class' => 'btn btn-sm  btn-primary']) ?>
        <?= Html::a('<i class="fa fa-plus-circle" aria-hidden="true"></i> Thêm mới', Url::to(['/customer/create']), ['class' => 'btn btn-sm btn-primary']) ?>
        <?php
        if (PermissionUtil::isAdminRole() || PermissionUtil::isHodRole()) {
            echo Html::button('<i class="fa fa-send-o" aria-hidden="true"></i> Chuyển nhân viên', ['class' => 'btn btn-sm  btn-primary', 'onclick' => 'showModalDelegate()']);
        }
        ?>
        <?= Html::button('<i class="fa fa-file-excel-o" aria-hidden="true"></i> Xuất excel', ['class' => 'btn btn-sm  btn-primary', 'onclick' => 'exportExcel()']); ?>
        <?php
        if (PermissionUtil::isAdminRole() || PermissionUtil::isHodRole()) {
            echo Html::button('<i class="fa fa-window-restore" aria-hidden="true"></i> Khôi phục đã xóa', ['class' => 'btn btn-sm  btn-success', 'onclick' => 'setActiveCustomer()']);
        }
        ?>
        <?php
        if(!DetectDeviceUtil::isMobile()) {
            echo Html::a('<i class="fa fa-file-excel-o" aria-hidden="true"></i> Nhập khách hàng từ excel', Url::to(['/import-customer']), ['class' => 'btn btn-sm  btn-success']);
        }?>
        <?= Html::button('<i class="fa fa-trash" aria-hidden="true"></i> Xóa', ['name' => 'btnDelete', 'class' => 'btn btn-sm  btn-danger', 'onclick' => "return deleteData('customer','delete')"]) ?>
    </div>
</div>
<?php ActiveForm::end(); ?>
<?php
if (PermissionUtil::isAdminRole() || PermissionUtil::isHodRole()) {
    ?>
    <div class="modal fade" id="modalDelegate" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title" id="myModalLabel">Chuyển khách hàng</h4>
                </div>
                <div class="modal-body form-horizontal">
                    <div class="form-group">
                        <label class="control-label col-xs-2 col-sm-2 col-md-2 col-lg-2"
                               for="customersearch-name">Phòng:</label>
                        <div class="col-xs-10 col-sm-10 col-md-10 col-lg-10">
                            <select id="department_id" class="form-control input-sm"
                                    onchange="loadNhanVien(this.value, 'staff_id')">
                                <option value="">--Chọn--</option>
                                <?php
                                foreach ($departments as $index => $department) { ?>
                                    <option value="<?= $department->id ?>"><?= $department->name ?></option>
                                    <?php
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-xs-2 col-sm-2 col-md-2 col-lg-2" for="customersearch-name">Nhân
                            viên:</label>
                        <div class="col-xs-10 col-sm-10 col-md-10 col-lg-10">
                            <select id="staff_id" class="form-control input-sm">
                                <option value="">--Chọn--</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <?= Html::button('<i class="fa fa-floppy-o" aria-hidden="true"></i> Lưu thông tin', ['class' => 'btn btn-sm btn-primary', 'onclick' => 'saveDelegate()']) ?>
                    <button type="button" class="btn btn-sm btn-danger" data-dismiss="modal"><i class="fa fa-arrow-left"  aria-hidden="true"></i> Đóng</button>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
<script type="text/javascript">
    function exportExcel() {
        var khachHang = $("#customersearch-name").val();
        var nguonKhachHang = $("#customersearch-chanel_id").val();
        var userId = $("#customersearch-user_id").val();
        $.ajax({
            url: '<?=Url::to(['/customer/export-excel'])?>',
            data: {
                khachHang: khachHang,
                nguon: nguonKhachHang,
                userId: userId
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

    function showModalDelegate() {
        $('#modalDelegate').modal('show');
    }

    function loadNhanVien(department_id, control_id) {
        $.ajax({
            url: '<?=Url::to(['/user-management/get-nhan-viens'])?>',
            data: {
                department_id: department_id,
                is_load_leader: 1,
                is_not_required: 1
            },
            type: "POST",
            beforeSend: function (xhr) {
                showLoading();
            },
            success: function (data) {
                $("#" + control_id).html(data);
                $(".chosen-select").trigger("chosen:updated");
                hideLoading();
            },
            error: function () {
                //alert("Lỗi hệ thống. Bạn không thể load dữ liệu");
                hideLoading();
            }
        });
        return false;
    }
<?php if (PermissionUtil::isAdminRole() || PermissionUtil::isHodRole()) { ?>
    function saveDelegate() {
        var staffId = $("#staff_id");
        var customers = getSelectedCheckboxGridView();
        if (customers.length == 0) {
            alert("Vui lòng chọn khách hàng");
            return;
        }
        if (staffId.val() == "" || staffId == null) {
            alert("Vui lòng chọn nhân viên");
            return;
        }
        $.ajax({
            url: '<?=Url::to(['/customer/save-delegate'])?>',
            data: {
                customers: getSelectedCheckboxGridView(),
                staff_id: staffId.val()
            },
            type: "POST",
            beforeSend: function (xhr) {
                showLoading();
            },
            success: function (data) {
                if (data == 1) {
                    window.location.reload();
                } else {
                    alert("Không thể chuyển khách hàng cho nhân viên khác");
                }
                hideLoading();
            },
            error: function () {
                alert("Lỗi hệ thống. Bạn không thể lưu dữ liệu");
                hideLoading();
            }
        });
    }
    function setActiveCustomer() {
        var ok = confirm("Bạn có muốn khôi phục khách hàng đã xóa");
        if(ok) {
            var customers = getSelectedCheckboxGridView();
            if (customers.length == 0) {
                alert("Vui lòng chọn khách hàng");
                return;
            }
            $.ajax({
                url: '<?=Url::to(['/customer/set-active-customer'])?>',
                data: {
                    customers: getSelectedCheckboxGridView()
                },
                type: "POST",
                beforeSend: function (xhr) {
                    showLoading();
                },
                success: function (data) {
                    if (data == 1) {
                        window.location.reload();
                    } else {
                        alert("Không thể khôi phục khách hàng đã xóa");
                    }
                    hideLoading();
                },
                error: function () {
                    alert("Lỗi hệ thống. Bạn không thể lưu dữ liệu");
                    hideLoading();
                }
            });
        }
    }
    <?php } ?>
</script>