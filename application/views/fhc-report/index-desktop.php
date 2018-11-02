<?php
/**
 * Created by PhpStorm.
 * User: phuocnguyen
 * Date: 05/09/2018
 * Time: 7:30 AM
 */

use yii\grid\GridView;
use application\models\Customer\CustomerUtil;
use application\models\FhcReport\DemandUtils;
use application\models\MaritalStatus\MaritalStatusUtil;
use application\utilities\DatetimeUtils;
use application\models\User\User;
use application\utilities\PermissionUtil;
use application\utilities\NumberUtils;
use application\models\Job\Job;

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

$name = array(
    'header' => 'Tên khách hàng',
    'content' => function ($model) {
        $customer = CustomerUtil::getCustomerByID($model->customer_id);
        return !is_null($customer) ? $customer->name : "";
    }
);

$birth_year = array(
    'header' => 'Tuổi',
    'attribute' => function ($model) {
        $customer = CustomerUtil::getCustomerByID($model->customer_id);
        if (!is_null($customer)) {
            if(DatetimeUtils::isDatetimeNotEmptyOrNull($customer->birthday)) {
                $namSinh = DatetimeUtils::formatDate($customer->birthday, 'Y');
                return date('Y') - $namSinh;
            }
            else{
                return "";
            }
        }
        return "";

    }
);

$phone = array(
    'header' => 'SĐT',
    'content' => function ($model) {
        $customer = CustomerUtil::getCustomerByID($model->customer_id);
        if (!is_null($customer)) {
            return $customer->phone;
        }
        return "";
    }
);

$diachi = array(
    'header' => 'Địa chỉ',
    'content' => function ($model) {
        $customer = CustomerUtil::getCustomerByID($model->customer_id);
        if (!is_null($customer)) {
            return $customer->address;
        }
        return "";
    }
);

$marital_status = array(
    'header' => 'Tình trạng hôn nhân',
    'content' => function ($model) {
        $customer = CustomerUtil::getCustomerByID($model->customer_id);
        if (!is_null($customer)) {
            return MaritalStatusUtil::getMaritalStatusName($customer->marital_status_id);
        }
        return "";
    }
);

$number_of_children = array(
    'header' => 'Số con',
    'content' => function ($model) {
        return $model->number_of_children;
    }
);


$demand = array(
    'header' => 'Nhu cầu',
    'attribute' => function ($model) {
        if (!empty($model->demand)) {
            $result = "";
            $array = explode(',', $model->demand);
            foreach ($array as $index => $item) {
                $ten = DemandUtils::getName($item);
                if (!empty($ten)) {
                    $result .= $ten . ", ";
                }
            }
            return $result;
        }
        return "";
    }
);

$nghe_nghiep = array(
    'header' => 'Nghề nghiệp',
    'attribute' => function ($model) {
        $job = Job::findOne(['id'=>$model->job_id]);
        if(!is_null($job)){
            return $job->name;
        }
        return "";
    }
);

$muc_luong = array(
    'header' => 'Mức lương',
    'attribute' => function ($model) {
        return NumberUtils::formatNumberWithDecimal($model->salary, 0);
    }
);


$sis = array(
    'header' => 'SIS',
    'content' => function ($model) {
        return ($model->sis == 1) ? "<font color='green'>Yes</font>" : "<font color='red'>No</font>";
    }
);

$khtn = array(
    'header' => 'KHTN',
    'attribute' => 'khtn',
    'content' => function ($model) {
        return $model->khtn;
    }
);

$jfw = array(
    'header' => 'JFW',
    'attribute' => 'jfw',
    'content' => function ($model) {
        return ($model->jfw == 1) ? "<font color='green'>Yes</font>" : "<font color='red'>No</font>";
    }
);

$nhanvien = array(
    'header' => 'Nhân viên',
    'content' => function ($model) {
        $user = User::findOne(['id' => $model->user_id]);
        if (!is_null($user)) {
            return $user->name;
        }
        return "";
    }
);
$columns = [
    $stt,
    $name,
    $birth_year,
    $phone,
    $diachi,
    $marital_status,
    $number_of_children,
    $demand,
    $nghe_nghiep,
    $muc_luong,
    $sis,
    $khtn
];
if (!PermissionUtil::isXPRole()) {
    $columns[] = $nhanvien;
}
echo GridView::widget([
    'tableOptions' => ['class' => 'table table-bordered table-hover'],
    'id' => 'list-data',
    'dataProvider' => $data,
    'columns' => $columns,
]);