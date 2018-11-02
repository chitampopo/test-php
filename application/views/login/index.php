<?php
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
?>
<div id="login-box" class="login-box visible widget-box no-border">
    <div class="widget-body">
        <div class="widget-main">
            <h4 class="header blue lighter bigger">
                <i class="ace-icon fa fa-coffee green"></i>
                Đăng nhập
            </h4>
            <div class="space-6"></div>
            <?php
            $form = ActiveForm::begin(['id' => 'login-form', 'options' => ['role' => 'form', 'class' => 'form-flatX']]);
            echo $form->field($model, 'username', ['inputTemplate' => '<div class="input-group"><span class="input-group-addon"><i class="fa fa-user"></i></span>{input}</div>'])->textInput(['placeholder' => $model->getAttributeLabel('username'), 'autofocus' => 'autofocus'])->label(false);
            echo $form->field($model, 'password', ['inputTemplate' => '<div class="input-group"><span class="input-group-addon"><i class="fa fa-key"></i></span>{input}</div>'])->passwordInput(['placeholder' => $model->getAttributeLabel('password')])->label(false);
            ?>
            <div class="form-group">
                <?= Html::submitButton('Đăng nhập', ['class' => 'btn btn-primary btn-block', 'name' => 'login-button']) ?>
            </div>
            <?php ActiveForm::end() ?>
        </div>
    </div>
</div>