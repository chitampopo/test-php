<?php
/**
 * Created by PhpStorm.
 * User: phuocnguyen
 * Date: 04/09/2018
 * Time: 11:35 PM
 */

use application\utilities\DetectDeviceUtil;

$this->title = "Danh sách khách hàng";
?>
<div class="breadcrumbs" id="breadcrumbs">
    <ul class="breadcrumb">
        <li>
            <i class="ace-icon fa fa-home home-icon"></i>
            <a href="#">Danh sách khách hàng</a>
        </li>
    </ul>
</div>
<br>
<div class="page-content">
    <?= $this->render('search', ['model' => $customerSearch, 'users' => $users,'categories'=>$categories,'departments'=>$departments]) ?>
    <?= $this->render('index-'.DetectDeviceUtil::getDevice(), ['data' => $data ]); ?>
</div>
