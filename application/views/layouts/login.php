<?php
use application\assets\AppAsset;
use yii\helpers\Html;
AppAsset::register($this);
$this->title = "Đăng nhập AIA CRM";
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel='shortcut icon' type='image/x-icon' href='favicon.ico' />
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>

</head>

<body class="login-layout">
<?php $this->beginBody() ?>
<div class="main-container">
    <div class="main-content">
        <div class="row">
            <div class="col-sm-10 col-sm-offset-1">
                <div class="login-container">
                    <div class="center">
                        <h1>
                            <img src="https://www.aia.com.vn/content/dam/aia/logos/aiawhite-logo.png" width="50px">
                        </h1>
                        <h4 style="color: #d31145" id="id-company-text">&copy; AIA CẦN THƠ</h4>
                    </div>

                    <div class="space-6"></div>

                    <div class="position-relative">
                        <?= $content ?>

                    </div><!-- /.position-relative -->
                </div>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.main-content -->
</div><!-- /.main-container -->
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
