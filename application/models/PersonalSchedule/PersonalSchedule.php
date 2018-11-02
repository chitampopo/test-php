<?php

namespace application\models\PersonalSchedule;

use application\utilities\DatetimeUtils;
use application\utilities\SessionUtils;
use yii\web\JsExpression;
use yii\db\ActiveRecord;

class PersonalSchedule extends ActiveRecord
{

    public $hour;
    public $minute;

    public static function tableName()
    {
        return '{{personal_schedule}}';
    }

    public function rules()
    {
        $rule = [
            ['customer_id', 'required', 'message' => 'Vui lòng chọn khách hàng'],
            ['is_new_customer', 'trim'],
            ['is_new_customer', 'required', 'message' => 'Vui lòng chọn có phải là khách hàng mới'],
            ['chanel_id', 'trim'],
            ['purpose_id', 'trim'],
            ['date', 'trim'],
            ['date', 'required', 'message' => 'Vui lòng chọn ngày thực hiện'],
            ['is_call', 'trim'],
            ['is_call', 'required', 'message' => 'Vui lòng chọn có phải cuộc gọi'],
            ['completed_date', 'trim'],
            ['completed_date', 'required', 'when' => function($model) {
                    return $model->completed == 1;
                }, 'whenClient' => "isScheduleCompletedChecked"
            ],
            ['fhc', 'trim'],
            ['sis', 'trim'],
            ['xuly', 'trim'],
            ['referral', 'trim'],
            ['recruiment', 'trim'],
            ['other', 'trim'],
            ['created_from', 'trim'],
            ['completed', 'default', 'value' => 0],
            ['user_id', 'default', 'value' => SessionUtils::getUserId()],
            ['created_at', 'default', 'value' => DatetimeUtils::getCurrentDatetime()],
            ['updated_at', 'default', 'value' => DatetimeUtils::getCurrentDatetime()],
            ['created_by', 'default', 'value' => SessionUtils::getUsername()],
            ['updated_by', 'default', 'value' => SessionUtils::getUsername()]
        ];
        return $rule;
    }

    public function attributeLabels()
    {
        return [
            'customer_id' => 'Khách hàng',
            'is_new_customer' => 'Mới',
            'chanel_id' => 'Nguồn',
            'purpose_id' => 'Mục đích',
            'date' => 'Ngày thực hiện',
            'is_call' => 'Kế hoạch cho',
            'completed' => 'Hoàn thành',
            'completed_date' => 'Ngày hoàn thành',
            'created_from' => 'Tạo từ',
            'fhc' => 'FHC',
            'sis' => 'SIS',
            'xuly' => 'Xử lý',
            'referral' => 'Referral',
            'recruiment' => 'Recruiment',
            'other' => 'Khác',
            'user_id' => 'Nhân viên',
        ];
    }
}