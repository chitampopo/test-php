<?php
/**
 * Created by PhpStorm.
 * User: Tam
 * Date: 04/09/2018
 * Time: 11:35 PM
 */

use application\utilities\DetectDeviceUtil;

$this->title = "Báo cáo trình bày FHC";
?>
<div class="breadcrumbs" id="breadcrumbs">
    <ul class="breadcrumb">
        <li>
            <i class="ace-icon fa fa-home home-icon"></i>
            <a href="#">Báo cáo trình bày FHC</a>
        </li>
    </ul>
</div>
<br>
<div class="page-content">
    <?= $this->render('search', ['model' => $fhcReportSearch,'users'=>$users]) ?>
    <?= $this->render('index-'.DetectDeviceUtil::getDevice(), ['data' => $data ]); ?>
</div>