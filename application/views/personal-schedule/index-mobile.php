<?php

use application\utilities\UrlUtils;
use application\utilities\PagingUtil;
use application\models\Customer\Customer;
use application\models\Chanel\Chanel;
use application\models\Purpose\Purpose;
use application\utilities\DatetimeUtils;
use application\utilities\PersonalScheduleUrlUtils;
use application\models\JfwSchedule\JfwSchedule;
use application\utilities\PermissionUtil;
use application\utilities\SessionUtils;
?>
<div class="clearfix"></div>
<div class="row">
    <?php
    $models = $data->getModels();
    $stt = 1;
    foreach ($models as $model) {
        $customer = Customer::findOne(['id' => $model->customer_id]) ?>
        <div class="col-xs-12 col-sm-12 col-md-4 col-lg-3">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <?= $stt++ . ". ".UrlUtils::buildEditLink('personal-schedule', $model->id, $customer->name);
                    $chanel = Chanel::findOne(['id' => $model->chanel_id]);
                    $purpose = Purpose::findOne(['id' => $model->purpose_id]);
                    $customer = Customer::findOne(['id' => $model->customer_id]);
                    ?>
                    <div class="pull-right">
                        <label style="margin-left: 10px;"><a href="javascript:void(0)"
                                                             onclick="deleteDataMobile('personal-schedule','delete','<?= $model->id ?>')"
                                                             style="color: red"><i class="fa fa-trash-o"></i>
                                Xóa</a></label>
                    </div>
                </div>
                <table class="table table-bordered">
                    <tr>
                        <td width="100px">SĐT:</td>
                        <td> <?= !is_null($customer) ? ("<a href='tel:" . $customer->phone . "'>" . $customer->phone . "</a>") : "" ?></td>
                    </tr>
                    <tr>
                        <td>Giờ:</td>
                        <td> <?= DatetimeUtils::formatDate($model->date, "H:i") ?></td>
                    </tr>
                    <tr>
                        <td>Khách mới:</td>
                        <td> <?= ($model->is_new_customer == 1) ? "<font color='green'>Yes</font>" : "<font color='red'>No</font>" ?></td>
                    </tr>
                    <tr>
                        <td>Nguồn:</td>
                        <td> <?= !is_null($chanel) ? $chanel->name : "" ?></td>
                    </tr>
                    <tr>
                        <td>Mục đích:</td>
                        <td> <?= !is_null($purpose) ? $purpose->name : "" ?></td>
                    </tr>
                    <?php
                    if(!PermissionUtil::isXPRole()){
                    ?>
                    <tr>
                        <td colspan="2">
                            <table width="100%">
                                <tr>
                                    <td><label><input type="checkbox" <?= $model->fhc == 1 ? 'checked' : '' ?> disabled> FHC</label></td>
                                    <td><label><input type="checkbox" <?= $model->sis == 1 ? 'checked' : '' ?> disabled> SIS</label></td>
                                    <td><label><input type="checkbox" <?= $model->xuly == 1 ? 'checked' : '' ?>  disabled> Xử lý</label></td>
                                </tr>
                                <tr>
                                    <td>Referral: <?= $model->referral ?></td>
                                    <td>Recruiment: <?= $model->recruiment ?></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td colspan="3">Khác: <?= $model->other ?></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <?php } ?>
                    <?php
                    if($model->user_id == SessionUtils::getUserId()){
                    ?>
                    <tr>
                        <td>Hoàn thành:</td>
                        <td><label><input disabled onchange="toggleComplete(this)" type="checkbox" <?= ($model->completed == 1) ? 'checked' : '' ?>  id='schedule-id-<?= $model->id ?>' ref-data='<?= $model->id ?>'/> Hoàn thành</label></td>
                    </tr>
                    <?php } ?>
                    <tr>
                        <td>Ngày HT:</td>
                        <td id="completed-date-<?= $model->id ?>"> <?= DatetimeUtils::isDatetimeNotEmptyOrNull($model->completed_date) ? DatetimeUtils::formatDate($model->completed_date) : "" ?></td>
                    </tr>
                    <tr>
                        <td>JFW</td>
                        <td>
                            <?php
                            $jfwSchedule = JfwSchedule::find()->where(['xp_schedule_id' => $model->id])->count();
                            if ($jfwSchedule > 0) {
                                echo "<font color='green'>Yes</font>";
                            }
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Hành động</td>
                        <td>
                            <?php
                            if ($model->completed) {
                                echo "";
                            } else {
                                echo PersonalScheduleUrlUtils::buildUrl($model->is_call, $model->completed, $model->id);
                            }
                            ?>
                        </td>
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
        <?= PagingUtil::buildSelectPage($data->getTotalCount(), 'personal-schedule') ?>
    </div>
    <div class="col-xs-4 col-sm-4 col-md-4 text-right"><?= PagingUtil::buildNextPage($data->getTotalCount()) ?></div>
</div>
