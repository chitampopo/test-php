<?php

namespace application\models\User;
use yii\db\ActiveRecord;
use application\utilities\SessionUtils;
use application\utilities\DatetimeUtils;
use application\validator\UsernameValidator;
use Yii;

class UserInfo extends ActiveRecord
{
    public static function tableName()
    {
        return '{{user}}';
    }

    public function rules()
    {
        $rule = [
            ['name', 'trim'],
            ['name', 'required','message'=>'Vui lòng nhập họ tên'],
            ['name', 'string', 'max' => 100],
            ['username', 'trim'],
            ['username', 'required','message'=>'Vui lòng nhập tên người dùng'],
            ['username', 'string', 'max' => 100],
            ['username', UsernameValidator::className()],
            ['phone', 'trim'],
            ['phone', 'string', 'max' => 100],
            ['email', 'trim'],
            ['email', 'string', 'max' => 100],
            ['email', 'required','message'=>'Vui lòng nhập thư điện tử'],
            ['address', 'trim'],
            ['address', 'string', 'max' => 500],
            ['level_id','trim'],
            ['level_id', 'required','message'=>'Vui lòng chọn chức vụ'],
            ['department_id','trim'],
            ['department_id', 'required','message'=>'Vui lòng chọn phòng ban'],
            ['is_active','trim'],
            ['is_active','default', 'value' => true],
            ['password_hash', 'trim'],
            ['auth_key', 'trim'],
            ['last_login_date', 'trim'],
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
            'name' => 'Họ tên',
            'username' => 'Tên đăng nhập',
            'phone' => 'Số điện thoại',
            'email' => 'Thư điện tử',
            'address' => 'Địa chỉ',
            'level_id' => 'Chức vụ',
            'department_id' => 'Phòng ban',
            'is_active' => 'Trạng thái'
        ];
    }
}