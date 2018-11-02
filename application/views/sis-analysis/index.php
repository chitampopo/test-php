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

$this->title = "Báo cáo khách hàng chưa tham gia SIS";
?>
<div class="breadcrumbs" id="breadcrumbs">
    <ul class="breadcrumb">
        <li>
            <i class="ace-icon fa fa-home home-icon"></i>
            <a href="<?= Url::to(['/']) ?>">Trang chủ</a>
        </li>
        <li class="active">
            <a href="#">Báo cáo SIS</a>
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
                'label' => 'col-sm-2 col-md-2 col-lg-2',
                'offset' => 'col-sm-offset-4',
                'wrapper' => 'col-sm-10 col-md-10 col-lg-10'
            ],
        ],
    ]);
    ?>
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-9 col-lg-9">
                <?php
                if (\application\utilities\DetectDeviceUtil::isMobile()) {
                    echo $form->field($model, 'from_date')->textInput(['class' => 'form-control input-sm','type'=>'date']);
                    echo $form->field($model, 'to_date')->textInput(['class' => 'form-control input-sm','type'=>'date']);
                } else {
                    echo $form->field($model, 'from_date')->textInput(['class' => 'form-control input-sm date-picker']);
                    echo $form->field($model, 'to_date')->textInput(['class' => 'form-control input-sm date-picker']);
                }
                ?>

                <div class="form-group">
                    <label class="control-label col-sm-2 col-md-2 col-lg-2"></label>
                    <div class="col-sm-8 col-md-8 col-lg-8">
                        <?= Html::button('<i class="fa fa-file-excel-o" aria-hidden="true"></i> Xem báo cáo', ['class' => 'btn btn-sm btn-primary', 'onclick' => 'exportExcel()']) ?>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>


<script type="text/javascript">
    function exportExcel() {
        var from_date = $("#sisanalysis-from_date").val();
        var to_date = $("#sisanalysis-to_date").val();
        var user_id = $("#sisanalysis-user_id").val();

        $.ajax({
            url: '<?=Url::to(['/sis-analysis/export-excel'])?>',
            data: {
                from_date: from_date,
                to_date: to_date,
                user_id: user_id
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

