<?php
/**
 * Created by PhpStorm.
 * User: Tam
 * Date: 9/9/2018
 * Time: 2:28 PM
 */
namespace application\models\MeetingResult;
use application\models\FhcReport\FhcReport;
use yii\db\ActiveRecord;

class MeetingResult extends ActiveRecord
{
    const MEETING_RESULT = 'meeting-result';
    public $is_add_schedule;
    public $fhc_report;
    public $hour;
    public $minute;
    public $schedule_id;
    public static function tableName()
    {
        return '{{meeting_result}}';
    }
    public function relations()
    {
        return array(
            'meeting_result'=>array(self::HAS_MANY, 'customer', 'customer_id')
        );
    }
    public function rules()
    {
        $rule = [
            ['customer_id', 'required', 'message' => 'Vui lòng chọn khách hàng'],
            [['meeting_date', 'chanel_id'], 'required'],
            [['is_new_meeting', 'hd', 'fhc', 'sis', 'warm'], 'integer'],
            [['khtn', 'is_new_meeting', 'chanel_id', 'customer_id'], 'integer'],
            [['other', 'reject_reason','note','schedule_id'], 'trim'],
            [['follow_up_date', 'fhc_report'], 'safe']

        ];
        return $rule;
    }

    public function attributeLabels()
    {
        return [
            'customer_id' => 'Khách hàng',
            'chanel_id' => 'Nguồn',
            'meeting_date' => 'Ngày',
            'follow_up_date' => 'Follow up',
            'is_new_meeting' => 'Gặp mới',
            'hd' => 'HĐ',
            'fhc' => 'FHC',
            'user_id' => 'Nhân viên',
            'sis' => 'SIS',
            'warm' => 'Warm',
            'other' => 'Khác'
        ];
    }
}

