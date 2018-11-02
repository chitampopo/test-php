<?php

use application\utilities\PagingUtil;
use application\models\Customer\Customer;
use application\models\Chanel\Chanel;
use application\models\Purpose\Purpose;
use application\utilities\DatetimeUtils;
use application\utilities\SessionUtils;
use application\models\JfwSchedule\JfwSchedule;
use application\models\User\User;
?>
<div class="clearfix"></div>
<div class="row">
    <?php
    $models = $data->getModels();
    foreach ($models as $model) {
        $customer = Customer::findOne(['id' => $model->customer_id]) ?>
        <div class="col-xs-12 col-sm-12 col-md-4 col-lg-3">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <?= !is_null($customer) ? $customer->name : "";
                    $chanel = Chanel::findOne(['id' => $model->chanel_id]);
                    $purpose = Purpose::findOne(['id' => $model->purpose_id]);
                    $customer = Customer::findOne(['id' => $model->customer_id]);
                    $user = User::findOne(['id'=>$model->user_id]);
                    $jfwSchedule = JfwSchedule::find()->where(['xp_schedule_id' => $model->id, 'user_id' => SessionUtils::getUserId()])->one();
                    ?>
                    <div class="pull-right">
                        <label style="margin-left: 10px;">
                            <input type='checkbox' onchange='toggleUpdateJfw(this)' <?= !is_null($jfwSchedule) ? 'checked' : ''?> id='schedule-id-<?= $model->id?>' ref-data='<?= $model->id?>' <?= $model->completed == 1 ? 'disabled':''?>> Jfw
                        </label>
                    </div>
                </div>
                <table class="table table-bordered">
                    <tr>
                        <td width="100px">SĐT:</td>
                        <td> <?= !is_null($customer) ? ("<a href='tel:". $customer->phone ."'>". $customer->phone ."</a>") : "" ?></td>
                    </tr>
                    <tr>
                        <td>Khách mới:</td>
                        <td> <?= ($model->is_new_customer == 1) ? "<font color='green'>Yes</font>" : "<font color='red'>No</font>" ?></td>
                    </tr>
                    <tr>
                        <td>Giờ:</td>
                        <td> <?= DatetimeUtils::formatDate($model->date, 'H:i') ?></td>
                    </tr>
                    <tr>
                        <td>Nguồn:</td>
                        <td> <?= !is_null($chanel) ? $chanel->name : "" ?></td>
                    </tr>
                    <tr>
                        <td>Mục đích:</td>
                        <td> <?= !is_null($purpose) ? $purpose->name : "" ?></td>
                    </tr>
                    <tr>
                        <td>Nhân viên:</td>
                        <td> <?= !is_null($user) ? $user->name : "" ?></td>
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
        <?= PagingUtil::buildSelectPage($data->getTotalCount(), 'jfw-schedule') ?>
    </div>
    <div class="col-xs-4 col-sm-4 col-md-4 text-right"><?= PagingUtil::buildNextPage($data->getTotalCount()) ?></div>
</div>
