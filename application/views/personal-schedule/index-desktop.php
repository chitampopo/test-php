<?php

use yii\grid\GridView;
use application\utilities\UrlUtils;
use application\utilities\PersonalScheduleUrlUtils;
use application\models\Chanel\Chanel;
use application\models\Purpose\Purpose;
use application\utilities\DatetimeUtils;
use application\models\Customer\Customer;
use application\utilities\PermissionUtil;
use application\utilities\SessionUtils;
use application\models\JfwSchedule\JfwSchedule;

$colCheckbox = array(
    'class' => 'yii\grid\CheckboxColumn',
    'checkboxOptions' => function ($model) {
        return ["value" => $model->id];
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
        if (!is_null($customer)) {
            return UrlUtils::buildEditLink('personal-schedule', $model->id, !is_null($customer) ? $customer->name : "");
        }
        return "";
    }
);
$customer_phone = array(
    'header' => 'Điện thoại',
    'content' => function ($model) {
        $customer = Customer::findOne(['id' => $model->customer_id]);
        if (!is_null($customer)) {
            return !is_null($customer) ? ("<a href='tel:" . $customer->phone . "'>" . $customer->phone . "</a>") : "";
        }
        return "";
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
    'content' => function ($model) {
        return DatetimeUtils::formatDate($model->date, 'H') . ":" . DatetimeUtils::formatDate($model->date, 'i');
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
$fhc = array(
    'header' => 'FHC/SIS/XL',
    'attribute' => 'fhc',
    'headerOptions' => [
        'width' => 80
    ],
    'content' => function ($model) {
        $result = "";
        $result .= "FHC: " . (($model->fhc == 1) ? "<font color='green'>Yes</font>" : "<font color='red'>No</font>");
        $result .= "<br>SIS: " . (($model->sis == 1) ? "<font color='green'>Yes</font>" : "<font color='red'>No</font>");
        $result .= "<br>Xử lý: " . (($model->xuly == 1) ? "<font color='green'>Yes</font>" : "<font color='red'>No</font>");
        return $result;
    }
);
$referral = array(
    'attribute' => 'referral',
    'headerOptions' => [
        'width' => 40
    ],
    'content' => function ($model) {
        return !is_null($model->referral) ? $model->referral : "";
    }
);
$recruiment = array(
    'attribute' => 'recruiment',
    'headerOptions' => [
        'width' => 40
    ],
    'content' => function ($model) {
        return !is_null($model->recruiment) ? $model->recruiment : "";
    }
);
$other = array(
    'attribute' => 'other',
    'content' => function ($model) {
        return !is_null($model->other) ? $model->other : "";
    }
);
$is_call = array(
    'header'=>'Hành động',
    'attribute' => 'is_call',
    'headerOptions' => [
        'width' => 60
    ],
    'content' => function ($model) {
        if ($model->completed == 1) {
            return "";
        }
        return PersonalScheduleUrlUtils::buildUrl($model->is_call, $model->completed, $model->id);
    }
);
$completed = array(
    'attribute' => 'completed',
    'headerOptions' => [
        'width' => 30
    ],
    'contentOptions' => [
        'style' => 'text-align:center'
    ],
    'content' => function ($model) {
        if ($model->user_id == SessionUtils::getUserId()) {
            return "<input type='checkbox' onchange='toggleComplete(this)' " . (($model->completed == 1) ? 'checked' : '') . " id='schedule-id-" . $model->id . "' ref-data='" . $model->id . "'>";
        }
        return "";
    }
);
$completed_date = array(
    'attribute' => 'completed_date',
    'content' => function ($model) {
        return "<span id='completed-date-" . $model->id . "'>" . (DatetimeUtils::isDatetimeNotEmptyOrNull($model->completed_date) ? DatetimeUtils::formatDate($model->completed_date) : "") . "</span>";
    }
);
$jfw = array(
    'headerOptions' => [
        'width' => 30
    ],
    'label' => 'JFW',
    'contentOptions' => [
        'style' => 'text-align:center'
    ],
    'content' => function ($model) {
        $jfwSchedule = JfwSchedule::find()->where(['xp_schedule_id' => $model->id])->count();
        if ($jfwSchedule > 0) {
            return "<font color='green'>Yes</font>";
        }
        return "";
    }
);
$columns = [
    $colCheckbox,
    $stt,
    $customer_name,
    $customer_phone,
    $daytime,
    $is_new_customer,
    $chanel,
    $purpose
];
if (!PermissionUtil::isXPRole()) {
    $columns[] = $fhc;
    $columns[] = $referral;
    $columns[] = $recruiment;
    $columns[] = $other;
}
$columns[] = $completed;
$columns[] = $completed_date;
$columns[] = $is_call;
$columns[] = $jfw;
echo GridView::widget([
    'id' => 'list-data',
    'dataProvider' => $data,
    'columns' => $columns,
]);