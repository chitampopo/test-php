<?php
use yii\helpers\Url;
$this->title = "Trang chủ hệ thống AIA CRM";
?>
<div class="breadcrumbs ace-save-state" id="breadcrumbs">
    <ul class="breadcrumb">
        <li>
            <i class="ace-icon fa fa-home home-icon"></i>
            <a href="#">Home</a>
        </li>
        <li class="active">Dashboard</li>
    </ul><!-- /.breadcrumb -->
</div>
<style>
.bg-image{
    margin: auto;
    align-content: center;
    vertical-align: middle;

}
.logo-and-title{
    z-index: 9999;
    position: absolute;
    top: 0;
    left: <?=\application\utilities\DetectDeviceUtil::isMobile()? '0%':'30%'?>;
    top:40%
}
</style>
<div class="page-content">
    <div class="bg-image">
        <center><img src="<?=Url::to(['/images/banner.png'])?>" class="img-responsive"/></center>
        <!--<div class="logo-and-title">
            <center><img src="<?=Url::to(['/images/logo-aia.png'])?>" width="100px"/></center>
            <h3 align="center">HỆ THỐNG CRM AIA CHI NHÁNH CẦN THƠ</h3>
        </div>-->
    </div>


</div>