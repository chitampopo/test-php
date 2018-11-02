<?php
/**
 * Created by PhpStorm.
 * User: phuocnguyen
 * Date: 05/09/2018
 * Time: 9:28 PM
 */
use application\utilities\DetectDeviceUtil;
$this->title = "Danh sách cuộc gọi";
?>
<div class="breadcrumbs" id="breadcrumbs">
    <ul class="breadcrumb">
        <li>
            <i class="ace-icon fa fa-home home-icon"></i>
            <a href="#">Lịch sử cuộc gọi</a>
        </li>
    </ul>
</div>
<br>
<div class="page-content">
    <?= $this->render('search', ['callResultSearch' => $callResultSearch,'users'=>$users]) ?>
    <?= $this->render('index-'.DetectDeviceUtil::getDevice(), ['data' => $data]); ?>
</div>