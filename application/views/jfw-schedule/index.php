<?php
/**
 * Created by PhpStorm.
 * User: phuocnguyen
 * Date: 05/09/2018
 * Time: 9:28 PM
 */
use application\utilities\DetectDeviceUtil;
use yii\helpers\Url;
$this->title = "Kế hoạch nhân viên";
?>
<div class="breadcrumbs" id="breadcrumbs">
    <ul class="breadcrumb">
        <li>
            <i class="ace-icon fa fa-home home-icon"></i>
            <a href="#">Kế hoạch nhân viên</a>
        </li>
    </ul>
</div>
<br>
<div class="page-content">
    <?= $this->render('search', ['jfwScheduleSearch' => $jfwScheduleSearch,'users'=>$users, 'completedStatus'=>$completedStatus]) ?>
    <?= $this->render('index-'.DetectDeviceUtil::getDevice(), ['data' => $data]); ?>
</div>

<script type="text/javascript">
    function toggleUpdateJfw(source) {
        if (confirm('Bạn sẽ JFW với nhân viên này ?')) {
            var xp_schedule_id = $(source).attr('ref-data');
            var jfw = source.checked;
            $.ajax({
            url: '<?=Url::to(['/jfw-schedule/jfw-xp'])?>',
                data: {
                    xpScheduleId: xp_schedule_id,
                    isJfw: jfw ? 1:0
                },
                type: "POST",
                beforeSend: function (xhr) {
                    showLoading();
                },
                success: function (data) {
                    alert("Cập nhật thành công. Hãy kiểm tra trong kế hoạch cá nhân của bạn");
                    hideLoading();
                },
                error: function () {
                    alert("Lỗi hệ thống. Bạn không thể phục hồi mật khẩu");
                    hideLoading();
                }
            });
        } else {
            $(source).prop('checked', !source.checked); 
        }
        return false;
    }
</script>