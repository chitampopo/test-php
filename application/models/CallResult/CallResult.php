<?php
/**
 * Created by PhpStorm.
 * User: phuocnguyen
 * Date: 06/09/2018
 * Time: 8:57 PM
 */
namespace application\models\CallResult;
use application\utilities\DatetimeUtils;
use application\utilities\SessionUtils;
use yii\db\ActiveRecord;

class CallResult extends ActiveRecord
{
    const CALL_RESULT ='call-result';
    public $is_add_schedule;
    public $hour;
    public $minute;
    public $schedule_id;
    public static function tableName()
    {
        return '{{call_result}}';
    }
    public function relations()
    {
        return array(
            'call_result'=>array(self::HAS_MANY, 'customer', 'customer_id')
        );
    }
    public function rules()
    {
        $rule = [
            ['customer_id', 'required', 'message' => 'Vui lòng chọn khách hàng'],
            ['chanel_id', 'required', 'message' => 'Vui lòng chọn nguồn khách hàng'],
            ['call_date', 'required', 'message' => 'Vui lòng chọn ngày'],
            ['is_new_call', 'trim'],
            ['purpose_id', 'trim'],
            ['result', 'trim'],
            ['appointment_date', 'trim'],
            ['note', 'trim'],
            ['schedule_id', 'trim'],
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
            'chanel_id' => 'Nguồn',
            'call_date' => 'Ngày',
            'is_new_call' => 'Gọi mới',
            'purpose_id' => 'Mục đích',
            'result' => 'Kết quả',
            'user_id' => 'Nhân viên',
            'appointment_date' => 'Ngày hẹn',
            'is_add_schedule'=>'Thêm vào kế hoạch',
            'note'=>'Ghi chú'
        ];
    }
}