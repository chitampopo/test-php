<?php

use application\utilities\DetectDeviceUtil;

$this->title = "Danh sách phòng ban";
?>
<div class="breadcrumbs" id="breadcrumbs">
    <ul class="breadcrumb">
        <li>
            <i class="ace-icon fa fa-home home-icon"></i>
            <a href="#">Danh sách phòng ban</a>
        </li>
    </ul>
</div>
<br>
<div class="page-content">
    <?= $this->render('search', ['model' => $departmentSearch]) ?>
    <?= $this->render('index-'.DetectDeviceUtil::getDevice(), ['data' => $data]); ?>
</div>
