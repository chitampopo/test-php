<?php
/**
 * Created by PhpStorm.
 * User: phuocnguyen
 * Date: 05/09/2018
 * Time: 10:08 PM
 */
namespace application\models\Purpose;
use application\Utilities\DatetimeUtils;
use application\Utilities\SessionUtils;
use yii\db\ActiveRecord;
class Purpose extends ActiveRecord
{
    public static function tableName()
    {
        return '{{purpose}}';
    }

    public function rules()
    {
        $rule = [
            ['name', 'required','message'=>'Vui lòng nhập tên'],
            ['description', 'trim'],
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
            'name' => 'Tên',
            'description' => 'Mô tả'
        ];
    }
}