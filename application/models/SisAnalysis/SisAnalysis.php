<?php
/**
 * Created by PhpStorm.
 * User: phuocnguyen
 * Date: 20/09/2018
 * Time: 8:55 PM
 */
namespace application\models\SisAnalysis;
use yii\base\Model;

class SisAnalysis extends Model
{
    public $from_date;
    public $to_date;
    public $user_id;

    public function attributeLabels()
    {
        return [
            'from_date' => 'Từ ngày',
            'to_date' => 'Đến ngày',
            'user_id' => 'Nhân viên'
        ];
    }

}