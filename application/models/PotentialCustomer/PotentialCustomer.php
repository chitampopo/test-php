<?php
/**
 * Created by PhpStorm.
 * User: phuocnguyen
 * Date: 09/09/2018
 * Time: 4:02 PM
 */

namespace application\models\PotentialCustomer;

use application\utilities\DatetimeUtils;
use application\utilities\SessionUtils;
use yii\db\ActiveRecord;

class PotentialCustomer extends ActiveRecord
{
    const POTENTIAL_RESULT ='potential-customer';
    public $is_add_schedule;
    public $hour;
    public $minute;
    public static function tableName()
    {
        return '{{potential_customers}}';
    }

    public function rules()
    {
        $rule = [
            ['customer_id', 'required', 'message' => 'Vui lòng chọn khách hàng'],
            ['customer_referral_id', 'trim'],
            ['chanel_id',  'required', 'message' => 'Vui lòng chọn nguồn'],
            ['scheduled_meeting_date', 'trim'],
            ['date', 'required', 'message' => 'Vui lòng chọn ngày'],
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
            'name'=>'Tên',
            'customer_id' => 'Khách hàng',
            'customer_referral_id' => 'Người giới thiệu',
            'chanel_id' => 'Nguồn',
            'scheduled_meeting_date' => 'Ngày gọi',
            'date' => 'Ngày',
            'user_id' => 'Nhân viên',
        ];
    }

}