<?php

namespace application\models\JfwSchedule;

use application\utilities\DatetimeUtils;
use application\utilities\SessionUtils;
use yii\web\JsExpression;
use yii\db\ActiveRecord;

class JfwSchedule extends ActiveRecord
{

    public static function tableName()
    {
        return '{{jfw_schedule}}';
    }

    public function rules()
    {
        $rule = [
            ['created_at', 'default', 'value' => DatetimeUtils::getCurrentDatetime()],
            ['created_by', 'default', 'value' => SessionUtils::getUsername()]
        ];
        return $rule;
    }
}