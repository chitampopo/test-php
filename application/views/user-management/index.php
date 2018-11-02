<?php

use application\utilities\DetectDeviceUtil;
use yii\helpers\Url;

$this->title = "Danh sách người dùng";
?>
<div class="breadcrumbs" id="breadcrumbs">
    <ul class="breadcrumb">
        <li>
            <i class="ace-icon fa fa-home home-icon"></i>
            <a href="#">Danh sách người dùng</a>
        </li>
    </ul>
</div>
<br>
<div class="page-content">
    <?= $this->render('search', ['model' => $userInfoSearch]) ?>
    <?= $this->render('index-'.DetectDeviceUtil::getDevice(), ['data' => $data]); ?>
</div>

<script type="text/javascript">
    function resetPassword(source) {
        if (confirm('Bạn có chắc chắn không ?')) {
            var reset_user = $(source).attr('reset-user');
            $.ajax({
            url: '<?=Url::to(['/user-management/reset-password'])?>',
                data: {
                    userId: reset_user
                },
                type: "POST",
                beforeSend: function (xhr) {
                    showLoading();
                },
                success: function (data) {
                    if (data == "success") {
                        alert("Phục hồi thành công. Hãy kiểm tra lại email");
                    }
                    hideLoading();
                },
                error: function () {
                    alert("Lỗi hệ thống. Bạn không thể phục hồi mật khẩu");
                    hideLoading();
                }
            });
        }
        return false;
    }
</script>
