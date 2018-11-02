<?php
/**
 * Created by PhpStorm.
 * User: phuocnguyen
 * Date: 05/09/2018
 * Time: 7:30 AM
 */

use application\utilities\UrlUtils;
use application\utilities\PagingUtil;
use application\models\Customer\Customer;
use application\models\Chanel\Chanel;
use application\models\Purpose\Purpose;
use application\utilities\DatetimeUtils;
use application\utilities\PermissionUtil;
use application\models\User\User;

?>
<div class="clearfix"></div>
<div class="row">
    <?php
    $models = $data->getModels();
    $stt =1;
    foreach ($models as $model) {
        $customer = Customer::findOne(['id' => $model->customer_id]) ?>
        <div class="col-xs-12 col-sm-12 col-md-4 col-lg-3">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <?php
                    $isEditable = PermissionUtil::userCanNotEditable($model);
                    if ($isEditable) {
                        echo "<strong>".$stt.". ".$customer->name."</strong>";
                    }else{
                        echo "<strong>".$stt.". ".UrlUtils::buildEditLink('call-result', $model->id, $customer->name)."</strong>";
                    }
                    $stt++;
                    $chanel = Chanel::findOne(['id' => $model->chanel_id]);
                    $purpose = Purpose::findOne(['id' => $model->purpose_id]);
                    if(!$isEditable){
                    ?>
                    <div class="pull-right">
                        <label style="margin-left: 10px;"><a href="javascript:void(0)" onclick="deleteDataMobile('call-result','delete','<?= $model->id ?>')" style="color: red"><i class="fa fa-trash-o"></i> Xóa</a></label>
                    </div>
                    <?php } ?>
                </div>
                <table class="table table-bordered">
                    <tr>
                        <td width="80px">SĐT:</td>
                        <td><a href="tel:<?= $customer->phone ?>"><?= $customer->phone ?></a> </td>
                    </tr>
                    <tr>
                        <td>Gọi mới:</td>
                        <td> <?= ($model->is_new_call == 1) ? "<font color='green'>Yes</font>" : "<font color='red'>No</font>" ?></td>
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
                        <td>Kết quả:</td>
                        <td> <?= ($model->result == 1) ? "<font color='green'>Yes</font>" : "<font color='red'>No</font>" ?></td>
                    </tr>
                    <tr>
                        <td>Ngày hẹn:</td>
                        <td> <?= DatetimeUtils::isDatetimeNotEmptyOrNull($model->appointment_date) ? DatetimeUtils::formatDate($model->appointment_date,"d/m/Y H:i") : "" ?></td>
                    </tr>

                    <?php
                    if(!PermissionUtil::isXPRole()){
                        $user = User::findOne(['id'=>$model->user_id]);
                        if(!is_null($user)){?>
                    <tr>
                        <td>Nhân viên:</td>
                        <td> <?= $user->name ?></td>
                    </tr>
                    <?php }
                    }
                    ?>
                </table>
            </div>
        </div>
        <?php
    } ?>
</div>
<div class="row">
    <div class="col-xs-4 col-sm-4 col-md-4 text-left"><?= PagingUtil::buildPrevPage() ?></div>
    <div class="col-xs-4 col-sm-4 col-md-4">
        <?= PagingUtil::buildSelectPage($data->getTotalCount(), 'call-result') ?>
    </div>
    <div class="col-xs-4 col-sm-4 col-md-4 text-right"><?= PagingUtil::buildNextPage($data->getTotalCount()) ?></div>
</div>
