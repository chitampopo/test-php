<?php
/**
 * Created by PhpStorm.
 * User: phuocnguyen
 * Date: 12/08/2018
 * Time: 6:47 AM
 */

namespace application\models\ChangePassword;

use yii\db\ActiveRecord;

class ChangePassword extends ActiveRecord
{
    public $currentPassword;
    public $newPassword;
    public $repeatNewPassword;

    public static function tableName()
    {
        return '{{user}}';
    }

    public function rules()
    {
        $rule = [
            ['currentPassword', 'required', 'message' => 'Vui lòng nhập mật khẩu hiện tại'],
            ['newPassword', 'required', 'message' => 'Vui lòng nhập mật khẩu mới'],
            ['repeatNewPassword', 'required', 'message' => 'Vui lòng nhập lại mật khẩu mới'],
            ['repeatNewPassword', 'compare', 'compareAttribute' => 'newPassword', 'message' => "Nhập lại mật khẩu mới không khớp"]
        ];
        return $rule;
    }

    public function attributeLabels()
    {
        return [
            'currentPassword' => 'Mật khẩu hiện tại',
            'newPassword' => 'Mật khẩu mới',
            'repeatNewPassword' => 'Nhập lại mật khẩu mới'
        ];
    }
}