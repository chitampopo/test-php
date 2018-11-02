<?php

use yii\helpers\Html;
use yii\helpers\Url;
use application\utilities\UrlUtils;
use application\utilities\PagingUtil;
use application\models\Level\Level;
use application\models\Department\Department;
?>

<div class="clearfix"></div>
<div class="row">
    <?php
    $models = $data->getModels();
    foreach ($models as $model) { ?>
        <div class="col-xs-12 col-sm-12 col-md-4 col-lg-3">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <?= UrlUtils::buildEditLink('user-management', $model->id, $model->name);
                        $level = Level::findOne(['id' => $model->level_id]);
                        $department = Department::findOne(['id' => $model->department_id]);?>
                    <div class="pull-right">
                        <label style="margin-left: 10px;"><a href="javascript:void(0)" onclick="deleteDataMobile('user-management','delete','<?=$model->id?>')" style="color: red"><i class="fa fa-trash-o"></i> Xóa</a> </label>
                    </div>
                </div>
                <table class="table table-bordered">
                    <tr>
                        <td width="80px">Tên đăng nhập:</td>
                        <td> <?= $model->username ?></td>
                    </tr>
                    <tr>
                        <td width="80px">SĐT:</td>
                        <td> <?= "<a href='tel:". $model->phone ."'>". $model->phone ."</a>" ?></td>
                    </tr>
                    <tr>
                        <td width="80px">Email:</td>
                        <td> <?= $model->email ?></td>
                    </tr>
                    <tr>
                        <td width="80px">Địa chỉ:</td>
                        <td> <?= $model->address ?></td>
                    </tr>
                    <tr>
                        <td width="80px">Chức vụ:</td>
                        <td> <?= $level->name ?></td>
                    </tr>
                    <tr>
                        <td width="80px">Phòng ban:</td>
                        <td> <?= $department->name ?></td>
                    </tr>
                </table>
            </div>
        </div>
        <?php
    } ?>
</div>
<div class="row">
    <div class="col-xs-4 col-sm-4 col-md-4 text-left"><?= PagingUtil::buildPrevPage() ?></div>
    <div class="col-xs-4 col-sm-4 col-md-4">
        <?=PagingUtil::buildSelectPage($data->getTotalCount(),'user-management')?>
    </div>
    <div class="col-xs-4 col-sm-4 col-md-4 text-right"><?= PagingUtil::buildNextPage($data->getTotalCount()) ?></div>
</div>
