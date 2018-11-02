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
use application\models\Purpose\Purpose;
use application\utilities\DatetimeUtils;
use application\models\Customer\Customer;
use application\models\User\User;
use application\utilities\PermissionUtil;
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
        return UrlUtils::buildEditLink('call-result', $model->id, $customer_name);
    }
);
$customer_phone = array(
    'header' => 'Điện thoại',
    'content' => function ($model) {
        $customer = Customer::findOne(['id' => $model->customer_id]);
        return !is_null($customer) ? $customer->phone : "";
    }
);


$is_new_call = array(
    'attribute' => 'is_new_call',
    'content' => function ($model) {
        return ($model->is_new_call == 1) ? "<font color='green'>Yes</font>" : "<font color='red'>No</font>";
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

$result = array(
    'attribute' => 'result',
    'content' => function ($model) {
        return ($model->result == 1) ? "<font color='green'>Yes</font>" : "<font color='red'>No</font>";
    }
);

$ngay_hen = array(
    'attribute' => 'appointment_date',
    'content' => function ($model) {
        return DatetimeUtils::isDatetimeNotEmptyOrNull($model->appointment_date) ? DatetimeUtils::formatDate($model->appointment_date, "d/m/Y H:i"):"";
    }
);


$columns = [
    $colCheckbox,
    $stt,
    $customer_name,
    $customer_phone,
    $is_new_call,
    $chanel,
    $purpose,
    $result,
    $ngay_hen,
];

if(!PermissionUtil::isXPRole()){
    $nhanVien = array(
        'attribute' => 'user_id',
        'content' => function ($model) {
            $user = User::findOne(['id'=>$model->user_id]);
            if(!is_null($user)){
                return $user->name;
            }
            return "";
        }
    );
    $columns[]= $nhanVien;
}
echo GridView::widget([
    'id' => 'list-data',
    'dataProvider' => $data,
    'columns' => $columns,
]);