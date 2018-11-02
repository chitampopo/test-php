<?php
/**
 * Created by PhpStorm.
 * User: phuocnguyen
 * Date: 14/09/2018
 * Time: 7:10 PM
 */
namespace application\models\DailyReview;
use yii\base\Model;

class DailyReview extends Model
{
    public $date;
    public $user_id;
    public $department_id;

    public function attributeLabels()
    {
        return [
            'date' => 'Ngày',
            'user_id' => 'Nhân viên',
            'department_id' =>'Phòng'
        ];
    }
}