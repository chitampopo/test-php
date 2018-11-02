<?php
/**
 * Created by PhpStorm.
 * User: phuocnguyen
 * Date: 17/09/2018
 * Time: 7:41 PM
 */

namespace application\models\DepartmentShedule;

use yii\base\Model;

class DepartmentShedule extends Model
{
    public $date;
    public $user_id;
    public $department_id;

    public function attributeLabels()
    {
        return [
            'date' => 'Ngày',
            'user_id' => 'Nhân viên',
            'department_id' => 'Phòng'
        ];
    }
}