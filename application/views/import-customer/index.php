<?php


use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

$this->title = 'Danh sách tra cứu hồ sơ';
?>
<div class="breadcrumbs" id="breadcrumbs">
    <ul class="breadcrumb">
        <li>
            <i class="ace-icon fa fa-home home-icon"></i>
            <a href="<?= Url::to(['/']) ?>">Trang chủ</a>
        </li>
        <li class="active">
            <a href="#">Nhập khách hàng từ excel</a>
        </li>
    </ul>
</div>
<br>

<div class="page-content">
    <?php
    $form = ActiveForm::begin([
        'id' => 'form-signup',
        'options' => ['enctype' => 'multipart/form-data'],
        'layout' => 'horizontal',
        'fieldConfig' => [
            'template' => "{label}\n{beginWrapper}\n{input}\n{hint}\n{error}\n{endWrapper}",
            'horizontalCssClasses' => [
                'label' => 'col-sm-2 col-md-2 col-lg-2',
                'offset' => '',
                'wrapper' => 'col-sm-10 col-md-10 col-lg-10'
            ],
        ],
    ]);
    ?>
    <?= $form->field($model_upload, 'filedinhkem')->fileInput() ?>
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <div class="form-group">
                <label class="control-label col-sm-4 col-md-4 col-lg-4"></label>
                <div class="col-sm-8 col-md-8 col-lg-8">
                    <?= Html::submitButton('<i class="fa fa-upload" aria-hidden="true"></i> Upload file', ['class' => 'btn btn-sm btn-primary', 'name' => 'btnSave']) ?>
                    <?= Html::button('<i class="fa fa-floppy-o" aria-hidden="true"></i> Cập nhật dữ liệu', ['class' => 'btn btn-sm btn-primary', 'onclick' => 'saveImport()']) ?>
                    <?= Html::button('<i class="fa fa-download" aria-hidden="true"></i> Tải file mẫu', ['class' => 'btn btn-sm btn-success', 'onclick' => 'exportExcel()']) ?>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <table class="table table-striped table-bordered">
            <thead>
            <tr>
                <th width="10">STT</th>
                <th width="20px"></th>
                <th>Họ tên</th>
                <th>Giới tính</th>
                <th>Năm sinh</th>
                <th>Điện thoại</th>
                <th>Email</th>
                <th>Địa chỉ</th>
                <th>Phân loại</th>
                <th>Tình trạng hôn nhân</th>
                <th>Công việc</th>
                <th>Nguồn</th>
                <th>Lương</th>
                <th>HĐ</th>
                <th>FHC</th>
                <th>SIS</th>
                <th>Error</th>

            </tr>
            </thead>
            <tbody>
            <?php
            if (!empty($data)) {
                $i = 1;
                foreach ($data as $id => $datum) {
                    if (isset($datum)) {
                        ?>
                        <tr class="<?=count($datum['errors']) > 0 ? "danger":""?>">
                            <td><?= $i++ ?></td>
                            <td><input type="checkbox" value="<?= $id ?>" name="ids[]" <?=count($datum['errors']) > 0 ? "disabled":""?> <?=count($datum['errors']) > 0 ? "":"checked"?>></td>
                            <td><?= $datum['name'] ?></td>
                            <td><?= $datum['sex'] ?></td>
                            <td><?= $datum['dateOfBirth'] ?></td>
                            <td><?= $datum['phone'] ?></td>
                            <td><?= $datum['email'] ?></td>
                            <td><?= $datum['address'] ?></td>
                            <td><?= $datum['category'] ?></td>
                            <td><?= $datum['marialStatus'] ?></td>
                            <td><?= $datum['job'] ?></td>
                            <td><?= $datum['chanel'] ?></td>
                            <td><?= \application\utilities\NumberUtils::formatNumberWithDecimal($datum['salary'],0) ?></td>
                            <td><?= $datum['hd'] ?></td>
                            <td><?= $datum['fhc'] ?></td>
                            <td><?= $datum['sis'] ?></td>
                            <td>
                                <?php
                                if (count($datum['errors']) > 0) { ?>
                                    <ul>
                                        <?php foreach ($datum['errors'] as $index => $error) { ?>
                                            <li><?= $error ?></li>
                                        <?php } ?>
                                    </ul>
                                <?php
                                }
                                ?>
                            </td>
                        </tr>
                        <?php
                    }
                }
            }
            ?>
            </tbody>
        </table>
    </div>
    <?php ActiveForm::end(); ?>
</div>
<script type="text/javascript">
    function exportExcel() {
        $.ajax({
            url: '<?=Url::to(['/import-customer/download-example-file'])?>',
            data: {},
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
    function saveImport() {
        var ids = $("input[name='ids[]']").val();
        var selected = [];
        $('input[type=checkbox]').each(function() {
            if ($(this).is(":checked")) {
                selected.push($(this).attr('value'));
            }
        });
        var ok = confirm("Bạn có chắc chắn muốn import các khách hàng đã chọn?");
        if(ok) {
            if(selected.length>0) {
                $.ajax({
                    url: '<?=Url::to(['/import-customer/save-import-customer'])?>',
                    data: {
                        ids: selected
                    },
                    type: "POST",
                    beforeSend: function (xhr) {
                        showLoading();
                    },
                    success: function (data) {
                        hideLoading();
                        if(data==1){
                            alert("Đã cập nhật thành công");
                            window.location.href='<?=Url::to(['/import-customer/'])?>';
                        }else{
                            alert("Có lỗi");
                        }
                    },
                    error: function () {
                        alert("Lỗi hệ thống. Không thể upload được dữ liệu");
                        hideLoading();
                    }
                });
            }else{
                alert('Chưa chọn tài sản để cập nhật');
            }
        }
    }
    $(document).ready(function () {
        $("#rowchkall").change(function(){
            if($(this).is(':checked')){
                $("input[type=checkbox]").each(function() {
                    $(this).attr('checked',true);
                });
            }else{
                $("input[type=checkbox]").each(function() {
                    $(this).attr('checked', false);
                });
            }
        });
    });
</script>
