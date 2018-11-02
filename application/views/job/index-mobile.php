<?php
/**
 * Created by PhpStorm.
 * User: phuocnguyen
 * Date: 05/09/2018
 * Time: 7:30 AM
 */
use application\utilities\UrlUtils;
use application\utilities\PagingUtil;
?>
<div class="clearfix"></div>
<div class="row">
    <?php
    $models = $data->getModels();
    foreach ($models as $model) { ?>
        <div class="col-xs-12 col-sm-12 col-md-4 col-lg-3">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <?= UrlUtils::buildEditLink('chanel', $model->id, $model->name); ?>
                    <div class="pull-right">
                        <label style="margin-left: 10px;"><a href="javascript:void(0)" onclick="deleteDataMobile('job','delete','<?=$model->id?>')" style="color: red"><i class="fa fa-trash-o"></i> Xóa</a> </label>
                    </div>
                </div>
                <div class="panel-body">
                    <?= $model->description ?>
                </div>
            </div>
        </div>
        <?php
    } ?>
</div>
<div class="row">
    <div class="col-xs-4 col-sm-4 col-md-4 text-left"><?= PagingUtil::buildPrevPage() ?></div>
    <div class="col-xs-4 col-sm-4 col-md-4">
        <?=PagingUtil::buildSelectPage($data->getTotalCount(),'job')?>
    </div>
    <div class="col-xs-4 col-sm-4 col-md-4 text-right"><?= PagingUtil::buildNextPage($data->getTotalCount()) ?></div>
</div>
