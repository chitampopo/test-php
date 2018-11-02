<?php

use yii\grid\GridView;
use application\utilities\UrlUtils;
use application\utilities\PermissionUtil;
use application\utilities\JfwScheduleUrlUtils;
use application\utilities\SessionUtils;
use application\models\JfwSchedule\JfwSchedule;
use application\models\Chanel\Chanel;
use application\models\Purpose\Purpose;
use application\utilities\DatetimeUtils;
use application\models\Customer\Customer;
use application\models\User\User;

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
        return !is_null($customer) ? $customer->name : "";
    }
);

$customer_phone = array(
    'header' => 'Điện thoại',
    'content' => function ($model) {
        $customer = Customer::findOne(['id' => $model->customer_id]);
        return !is_null($customer) ? ("<a href='tel:". $customer->phone ."'>". $customer->phone ."</a>") : "";
    }
);

$daytime = array(
    'attribute' => 'date',
    'label' => 'Giờ hẹn',
    'headerOptions' => [
        'width' => 50
    ],
    'contentOptions' => [
        'style' => 'text-align:center'
    ],
    'content' => function($model) {
        return DatetimeUtils::formatDate($model->date, 'H') .":". DatetimeUtils::formatDate($model->date, 'i');
    }
);

$is_new_customer = array(
    'attribute' => 'is_new_customer',
    'headerOptions' => [
        'width' => 20
    ],
    'contentOptions' => [
        'style' => 'text-align:center'
    ],
    'content' => function ($model) {
        return ($model->is_new_customer == 1) ? "<font color='green'>Yes</font>" : "<font color='red'>No</font>";
    }
);

$chanel = array(
    'attribute' => 'chanel_id',
    'content' => function ($model) {
        $chanel = Chanel::findOne(['id' => $model->chanel_id]);
        return !is_null($chanel) ? $chanel->name : "";
    }
);

$purpose = array(
    'attribute' => 'purpose_id',
    'content' => function ($model) {
        $purpose = Purpose::findOne(['id' => $model->purpose_id]);
        return !is_null($purpose) ? $purpose->name : "";
    }
);

$jfw = array(
    'header' => 'JFW',
    'headerOptions' => [
        'width' => 10
    ],
    'contentOptions' => [
        'style' => 'text-align:center'
    ],
    'content' => function ($model) {
        $jfwSchedule = JfwSchedule::find()->where(['xp_schedule_id' => $model->id, 'user_id' => SessionUtils::getUserId()])->one();
        return "<input type='checkbox' onchange='toggleUpdateJfw(this)' " . (!is_null($jfwSchedule) ? 'checked' : '') . " id='schedule-id-" . $model->id . "' ref-data='" . $model->id . "' " .($model->completed == 1 ? 'disabled':''). ">";
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
        $stt,
        $customer_name,
        $customer_phone,
        $daytime,
        $is_new_customer,
        $chanel,
        $purpose,
        $nhanvien,
        $jfw
    ],
]);