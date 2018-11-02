<?php
/**
 * Created by PhpStorm.
 * User: phuocnguyen
 * Date: 29/09/2018
 * Time: 10:10 AM
 */
namespace application\models\Job;
use application\utilities\DatetimeUtils;
use application\utilities\SessionUtils;
use yii\db\ActiveRecord;

class Job extends ActiveRecord
{
    public static function tableName()
    {
        return '{{job}}';
    }

    public function rules()
    {
        $rule = [
            ['name', 'required', 'message' => 'Vui lòng nhập tên'],
            ['description', 'trim'],
            ['created_at', 'default', 'value' => DatetimeUtils::getCurrentDatetime()],
            ['created_by', 'default', 'value' => SessionUtils::getUsername()]
        ];
        return $rule;
    }

    public function attributeLabels()
    {
        return [
            'name' => 'Tên',
            'description' => 'Mô tả'
        ];
    }
}