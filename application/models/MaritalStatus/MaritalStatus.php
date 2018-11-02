<?php
namespace application\models\MaritalStatus;
use application\Utilities\DatetimeUtils;
use application\Utilities\SessionUtils;

use yii\db\ActiveRecord;

class MaritalStatus extends ActiveRecord
{
    public static function tableName()
    {
        return '{{marital_status}}';
    }

    public function rules()
    {
        $rule = [
            ['name', 'trim'],
            ['name', 'required','message'=>'Vui lòng nhập tình trạng hôn nhân'],
            ['name', 'string', 'max' => 100],
            ['description', 'trim'],
            ['description', 'string', 'max' => 200],
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
            'name' => 'Tình trạng hôn nhân',
            'description' => 'Mô tả'
        ];
    }
}
