<?php
/**
 * Created by PhpStorm.
 * User: phuocnguyen
 * Date: 05/09/2018
 * Time: 7:30 AM
 */

use yii\grid\GridView;
use application\utilities\UrlUtils;
use application\models\CallResult\CallResultUtil;
use application\models\MeetingResult\MeetingResultUtil;
use application\utilities\DatetimeUtils;
use application\models\Job\Job;
use application\utilities\PermissionUtil;
use application\models\User\User;
use application\utilities\NumberUtils;
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

$name = array(
    'header' => 'Họ và tên',
    'content' => function ($model) {
        if(PermissionUtil::userCanNotEditable($model)){
            return $model->name;
        }
        return UrlUtils::buildEditLink('customer', $model->id, $model->name);
    }
);

$phone = array(
    'header' => 'Số điện thoại',
    'attribute' => 'phone',
    'content' => function ($model) {
        return "<a href='tel:". $model->phone ."'>". $model->phone ."</a>";
    }
);

$sex = array(
    'header' => 'Giới tính',
    'attribute' => 'sex',
    'headerOptions' => [
        'width' => 40
    ],
    'format' => 'raw',
    'value' => function ($data) {
        if (!is_null($data->sex)) {
            return $data->sex == 1 ? "Nam" : "Nữ";
        }
        return "";
    }
);

$age = array(
    'header' => 'Tuổi',
    'attribute' => 'birthday',
    'headerOptions' => [
        'width' => 10
    ],
    'contentOptions' => [
        'style' => 'text-align:center'
    ],
    'content' => function ($model) {
        $age = !is_null($model->birthday) ? date_diff(date_create($model->birthday), date_create('now'))->y : 0;
        return $age != 0 ? $age : "";
    }
);

$email = array(
    'header' => 'Email',
    'attribute' => 'email',
    'content' => function ($model) {
        return "<a href='mailto:{$model->email}'>{$model->email}</a>";
    }
);

$category = array(
    'header' => 'Phân loại',
    'attribute' => 'category',
    'content' => function($data){
        if($data->category == '0'){
            return 'Cold';
        }
        if($data->category == '1'){
            return 'Warm';
        }
        if($data->category == '2'){
            return 'Hot';
        }
    }
);

$job = array(
    'header' => 'Công việc',
    'attribute' => 'job_id',
    'content' => function($data){
        $content = "Thu nhập: ".NumberUtils::formatNumberWithDecimal($data->salary,0);
        $job = Job::findOne(['id'=>$data->job_id]);
        if(!is_null($job)){
            $content.= "<br>Công việc: ".$job->name;
        }else{
            $content.= "<br>Công việc: ";
        }
        return $content;
    }
);

$address = array(
    'header' => 'Địa chỉ',
    'attribute' => 'address'

);


$last_call_date = array(
    'header' => 'Gọi/gặp gần nhất',
    'content' => function ($model) {
        $content  = "";
        $latestCallResult = CallResultUtil::getLatestCallResultByCustomerId($model->id);
        $content = "Gọi:". (!is_null($latestCallResult) ? (DatetimeUtils::isDatetimeNotEmptyOrNull($latestCallResult->call_date) ? DatetimeUtils::formatDate($latestCallResult->call_date):"") : "");

        $latestMeetingResult = MeetingResultUtil::getLatestMeetingResultByCustomerId($model->id);
        $content .="<br>Gặp: ". (!is_null($latestMeetingResult) ? (DatetimeUtils::isDatetimeNotEmptyOrNull($latestMeetingResult->meeting_date) ? DatetimeUtils::formatDate($latestMeetingResult->meeting_date):"") : "");
        return $content;
    }
);

$columns= [
    $colCheckbox,
    $name,
    $sex,
    $age,
    $phone,
    $email,
    $job,
    $address,
    $category,
    $last_call_date
];
if(!PermissionUtil::isXPRole()){
    $nhanVien = array(
        'header' => 'Nhân viên',
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
$hd = array(
    'header' => 'HĐ',
    'headerOptions' => [
        'width' => 30
    ],
    'content' => function ($model) {
        return $model->hd =="Yes" ? "<font color='green'>Yes</font>" : "<font color='red'>No</font>";
    }
);
$fhc = array(
    'header' => 'FHC',
    'headerOptions' => [
        'width' => 30
    ],
    'content' => function ($model) {
        return $model->fhc =="Yes" ? "<font color='green'>Yes</font>" : "<font color='red'>No</font>";
    }
);
$sis = array(
    'header' => 'SIS',
    'headerOptions' => [
        'width' => 30
    ],
    'content' => function ($model) {
        return $model->sis =="Yes" ? "<font color='green'>Yes</font>" : "<font color='red'>No</font>";
    }
);
$columns[]=$hd;
$columns[]=$fhc;
$columns[]=$sis;
if(PermissionUtil::isHodRole() || PermissionUtil::isAdminRole()){
    $is_active = array(
        'header' => 'Đã xóa',
        'attribute' => 'is_active',
        'headerOptions' => [
            'width' => 60
        ],
        'content' => function ($model) {
            return $model->is_active == 0 ? "<font color='red'>Yes</font>" : "";
        }
    );
    $columns[]= $is_active;
}
$actions = array(
    'header' => 'Hành động',
    'headerOptions' => [
        'width' => 100
    ],
    'content' => function ($model) {
        $urlCallResult = Url::to(['/call-result/update?customer=' . $model->id]);
        $urlMeetingResult =  Url::to(['/meeting-result/update?customer=' . $model->id]);
        $urlCreateSchedule =  Url::to(['/personal-schedule/update?customer=' . $model->id]);
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
$columns [] = $actions;
echo GridView::widget([
    'tableOptions' => ['class' => 'table table-bordered table-hover'],
    'id' => 'list-data',
    'dataProvider' => $data,
    'columns' => $columns,
]);