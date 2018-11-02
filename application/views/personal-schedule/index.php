<?php
/**
 * Created by PhpStorm.
 * User: phuocnguyen
 * Date: 05/09/2018
 * Time: 9:28 PM
 */
use application\utilities\DetectDeviceUtil;
use yii\helpers\Url;
$this->title = "Kế hoạch cá nhân";
?>
<div class="breadcrumbs" id="breadcrumbs">
    <ul class="breadcrumb">
        <li>
            <i class="ace-icon fa fa-home home-icon"></i>
            <a href="#">Kế hoạch cá nhân</a>
        </li>
    </ul>
</div>
<br>
<div class="page-content">
    <?= $this->render('search', ['personalScheduleSearch' => $personalScheduleSearch, 'completedStatus'=>$completedStatus]) ?>
    <?= $this->render('index-'.DetectDeviceUtil::getDevice(), ['data' => $data]); ?>
</div>

<script type="text/javascript">
    function toggleComplete(source) {
        if (confirm('Bạn có chắc chắn không ?')) {
            var id = $(source).attr('ref-data');
            var complete = source.checked;
            $.ajax({
            url: '<?=Url::to(['/personal-schedule/update-complete'])?>',
                data: {
                    scheduleId: id,
                    isComplete: complete ? 1:0
                },
                type: "POST",
                beforeSend: function (xhr) {
                    showLoading();
                },
                success: function (completeDate) {
                    hideLoading();
                    $("#completed-date-" + id).text(completeDate);
                },
                error: function () {
                    alert("Lỗi hệ thống.");
                    hideLoading();
                }
            });
        }
        return false;
    }
</script>