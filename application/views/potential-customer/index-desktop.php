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
use yii\helpers\Url;

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
        if (PermissionUtil::userCanNotEditable($model)) {
            return $customer_name;
        }
        return UrlUtils::buildEditLink('potential-customer', $model->id, $customer_name);
    }
);
$customer_phone = array(
    'header' => 'Điện thoại',
    'content' => function ($model) {
        $customer = Customer::findOne(['id' => $model->customer_id]);
        return !is_null($customer) ? $customer->phone : "";
    }
);


$nguoi_gioi_thieu = array(
    'header' => 'Người giới thiệu',
    'content' => function ($model) {
        $customer = Customer::findOne(['id' => $model->customer_referral_id]);
        if (!is_null($customer)) {
            return $customer->name;
        }
        return "";
    }
);

$chanel = array(
    'attribute' => 'chanel_id',
    'content' => function ($model) {
        $chanel = Chanel::findOne(['id' => $model->chanel_id]);
        return !is_null($chanel) ? $chanel->name : "";
    }
);

$ngay_hen = array(
    'attribute' => 'scheduled_meeting_date',
    'content' => function ($model) {
        return DatetimeUtils::isDatetimeNotEmptyOrNull($model->scheduled_meeting_date) ? DatetimeUtils::formatDate($model->scheduled_meeting_date, "d/m/Y H:i") : "";
    }
);
$actions = array(
    'header' => 'Hành động',
    'headerOptions' => [
        'width' => 100
    ],
    'content' => function ($model) {
        $urlCallResult = Url::to(['/call-result/update?customer=' . $model->customer_id]);
        $urlMeetingResult =  Url::to(['/meeting-result/update?customer=' . $model->customer_id]);
        $urlCreateSchedule =  Url::to(['/personal-schedule/update?customer=' . $model->customer_id]);
        $content = '<div class="btn-group">
        <button data-toggle="dropdown" class="btn btn-info btn-sm dropdown-toggle" aria-expanded="true">Hành động<span class="ace-icon fa fa-caret-down icon-on-right"></span>
												</button>
												<ul class="dropdown-menu dropdown-info dropdown-menu-right">
													<li><a href="'.$urlCallResult.'"><i class="fa fa-phone"></i> Tạo KQ cuộc gọi</a></li>
													<li><a href="'.$urlMeetingResult.'"><i class="fa fa-handshake-o"></i> Tạo KQ cuộc gặp</a></li>
													<li><a href="'.$urlCreateSchedule.'"><i class="fa fa-calendar"></i> Tạo lịch hẹn</a></li>
												</ul>
											</div>';
        return $content;
    }
);
$columns = [
    $colCheckbox,
    $stt,
    $customer_name,
    $customer_phone,
    $nguoi_gioi_thieu,
    $chanel,
    $ngay_hen
];

if (!PermissionUtil::isXPRole()) {
    $nhanvien = array(
        'attribute' => 'user_id',
        'visible' => !PermissionUtil::isXPRole(),
        'content' => function ($model) {
            $user = User::findOne(['id' => $model->user_id]);
            if (!is_null($user)) {
                return $user->name;
            }
            return "";
        }
    );
    $columns[] = $nhanvien;
}
$columns[]=$actions;

echo GridView::widget([
    'id' => 'list-data',
    'dataProvider' => $data,
    'columns' => $columns,
]);