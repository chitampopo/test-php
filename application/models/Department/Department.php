<?php

namespace application\models\Department;
use application\utilities\DatetimeUtils;
use application\utilities\SessionUtils;
use yii\db\ActiveRecord;

class Department extends ActiveRecord
{
    public static function tableName()
    {
        return '{{department}}';
    }

    public function rules()
    {
        $rule = [
            ['name', 'trim'],
            ['name', 'required','message'=>'Vui lòng nhập tên phòng ban'],
            ['name', 'string', 'max' => 100],
            ['description', 'trim'],
            ['description', 'string', 'max' => 500],
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
            'name' => 'Tên phòng ban',
            'description' => 'Mô tả'
        ];
    }
}
