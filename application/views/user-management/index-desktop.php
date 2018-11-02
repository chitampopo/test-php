<?php

use yii\grid\GridView;
use application\utilities\UrlUtils;
use application\models\Level\Level;
use application\models\Department\Department;
use yii\helpers\Html;
use yii\helpers\Url;

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
$name = array(
    'attribute' => 'name',
    'content' => function ($model) {
        return UrlUtils::buildEditLink('user-management', $model->id, $model->name);
    }
);
$username = array(
    'attribute' => 'username'
);

$phone = array(
    'header' => 'Số điện thoại',
    'attribute' => 'phone',
    'content' => function ($model) {
        return "<a href='tel:". $model->phone ."'>". $model->phone ."</a>";
    }
);

$email = array(
    'header' => 'Email',
    'attribute' => 'email'
);

$address = array(
    'attribute' => 'address'
);

$level = array(
    'attribute' => 'level_id',
    'content' => function ($model) {
        $level = Level::findOne(['id' => $model->level_id]);
        return !is_null($level) ? $level->name : "";
    }
);

$department = array(
    'attribute' => 'department_id',
    'content' => function ($model) {
        $department = Department::findOne(['id' => $model->department_id]);
        return !is_null($department) ? $department->name : "";
    }
);

$is_active = array(
    'attribute' => 'is_active',
    'content' => function ($model) {
        return ($model->is_active == 1) ? "<font color='green'>Active</font>" : "<font color='red'>Inactive</font>";
    }
);

$reset_password = array(
    'content' => function ($model) {
        return "<a class='btn btn-xs btn-light' href='#' reset-user='". $model->id ."' onclick='resetPassword(this);'>Reset password</a>";
    }
);

$assined_team = array(
    'header' => 'Phân công phòng',
    'content' => function ($model) {
        if($model->level_id==5){
            $url = Url::to(['/xc-assigned-team?id='.$model->id]);
            return "<a href='{$url}'>Phân công</a>";
        }
    }
);

echo GridView::widget([
    'id' => 'list-data',
    'dataProvider' => $data,
    'columns' => [
        $colCheckbox,
        $stt,
        $name,
        $username,
        $phone,
        $email,
        $address,
        $level,
        $department,
        $is_active,
        $reset_password,
        $assined_team
    ],
]);
