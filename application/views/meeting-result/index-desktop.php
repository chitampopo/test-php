<?php
/**
 * Created by PhpStorm.
 * User: phuocnguyen
 * Date: 05/09/2018
 * Time: 7:30 AM
 */

use yii\grid\GridView;
use application\utilities\UrlUtils;
use application\models\Chanel\Chanel;
use application\utilities\DatetimeUtils;
use application\models\Customer\Customer;
use application\utilities\PermissionUtil;
use application\models\User\User;
use application\utilities\SessionUtils;
$colCheckbox = array(
    'class' => 'yii\grid\CheckboxColumn',
    'checkboxOptions' => function ($model) {
        return PermissionUtil::showCheckboxInListRalatedPermission($model);
    },
    'headerOptions' => [
        'width' => 10
    ],
);

$stt = array(
    'class' => 'yii\grid\SerialColumn',
    'header' => '#',
    'headerOptions' => [
        'width' => 10
    ],
    'contentOptions' => [
        'style' => 'text-align:center'
    ]
);

$customer_name = array(
    'attribute' => 'customer_id',
    'content' => function ($model) {
        $customer = Customer::findOne(['id' => $model->customer_id]);
        $customer_name = !is_null($customer) ? $customer->name : "";
        if(PermissionUtil::userCanNotEditable($model)){
            return $customer_name;
        }
        return UrlUtils::buildEditLink('meeting-result', $model->id, $customer_name);
    }
);

$customer_phone = array(
    'header' => 'Điện thoại',
    'content' => function ($model) {
        $customer = Customer::findOne(['id' => $model->customer_id]);
        return !is_null($customer) ? $customer->phone : "";
    }
);

$chanel = array(
    'attribute' => 'chanel_id',
    'content' => function ($model) {
        $chanel = Chanel::findOne(['id' => $model->chanel_id]);
        return !is_null($chanel) ? $chanel->name : "";
    }
);

$is_meeting = array(
    'format' => 'raw',
    'header' => 'Gặp<br>mới',
    'headerOptions' => [
        'width' => 10
    ],
    'content' => function ($model) {
        return !is_null($model->is_new_meeting) &&$model->is_new_meeting == 1 ? "<font color='green'>Yes</font>" : "<font color='red'>No</font>";
    }
);

$is_hd = array(
    'format' => 'raw',
    'header' => 'HĐ',
    'headerOptions' => [
        'width' => 10
    ],
    'content' => function ($model) {
        return !is_null($model->hd) &&$model->hd == 1 ? "<font color='green'>Yes</font>" : "<font color='red'>No</font>";
    }
);

$is_fhc = array(
    'format' => 'raw',
    'header' => 'FHC',
    'headerOptions' => [
        'width' => 10
    ],
    'content' => function ($model) {
        return !is_null($model->fhc) &&$model->fhc == 1 ? "<font color='green'>Yes</font>" : "<font color='red'>No</font>";
    }
);

$is_sis = array(
    'format' => 'raw',
    'header' => 'SIS',
    'headerOptions' => [
        'width' => 10
    ],
    'content' => function ($model) {
        return !is_null($model->sis) &&$model->sis == 1 ? "<font color='green'>Yes</font>" : "<font color='red'>No</font>";
    }
);

$is_warm = array(
    'format' => 'raw',
    'header' => 'WARM',
    'headerOptions' => [
        'width' => 10
    ],
    'content' => function ($model) {
        return !is_null($model->warm) &&$model->warm == 1 ? "<font color='green'>Yes</font>" : "<font color='red'>No</font>";
    }
);

$khtn = array(
    'header' => 'KHTN',
    'headerOptions' => [
        'width' => 50
    ],
    'content' => function ($model) {
        return !is_null($model->khtn) ? $model->khtn : "";
    }
);

$follow_up = array(
    'header' => 'Follow Up',
    'headerOptions' => [
        'width' => 150
    ],
    'content' => function ($model) {
        return DatetimeUtils::isDatetimeNotEmptyOrNull($model->follow_up_date) ? DatetimeUtils::formatDate($model->follow_up_date, "d/m/Y H:i") : "";
    }
);

$tuchoi = array(
    'header' => 'Lý do từ chối',
    'headerOptions' => [
        'width' => 150
    ],
    'content' => function ($model) {
        return !is_null($model->reject_reason) ? $model->reject_reason : "";
    }
);

$nhanvien = array(
    'attribute' => 'user_id',
    'visible' => !PermissionUtil::isXPRole(),
    'content' => function ($model) {
        $user = User::findOne(['id'=>$model->user_id]);
        if(!is_null($user)){
            return $user->name;
        }
        return "";
    }
);

echo GridView::widget([
    'id' => 'list-data',
    'dataProvider' => $data,
    'columns' => [
        $colCheckbox,
        $stt,
        $customer_name,
        $customer_phone,
        $chanel,
        $is_meeting,
        $is_hd,
        $is_fhc,
        $is_sis,
        $is_warm,
        $khtn,
        $follow_up,
        $tuchoi,
        $nhanvien
    ],
]);