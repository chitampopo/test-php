<?php
namespace application\models\XcAssignedTeam;
use application\utilities\DatetimeUtils;
use application\utilities\SessionUtils;
use yii\db\ActiveRecord;
class XcAssignedTeam extends ActiveRecord
{
    public static function tableName()
    {
        return '{{xc_assigned_team}}';
    }

    public function rules()
    {
        $rule = [
            ['user_id', 'required','message'=>'Vui lòng chọn nhân viên'],
            ['department_id', 'required','message'=>'Vui lòng chọn phòng ban'],
            ['created_at', 'default', 'value' => DatetimeUtils::getCurrentDatetime()],
            ['created_by', 'default', 'value' => SessionUtils::getUsername()]
        ];
        return $rule;
    }

    public function attributeLabels()
    {
        return [
            'user_id' => 'Tên nhân viên',
            'department_id' => 'Phòng ban'
        ];
    }
}