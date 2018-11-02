<?php
/**
 * Created by PhpStorm.
 * User: Tam
 * Date: 9/14/2018
 * Time: 7:45 PM
 */
namespace application\models\FhcReport;

use application\utilities\DatetimeUtils;
use application\utilities\SessionUtils;

class FhcReport extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return '{{fhc_report}}';
    }

    public function rules()
    {
        $rule = [
            [['customer_id'], 'required'],
            [['date'], 'trim'],
            [['customer_id', 'marital_status_id', 'demand', 'number_of_children', 'khtn', 'sis', 'jfw'], 'integer'],
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
            'customer_id' => 'Khách hàng',
            'user_id' => 'Nhân viên',
            'date' => 'Ngày',
            'address' => 'Địa chỉ',
            'job' => 'Nghề nghiệp',
            'marital_status' => 'Tình trạng hôn nhân',
            'number_of_children' => 'Số con',
            'demand' => 'Nhu cầu',
            'sis' => 'SIS',
            'khtn' => 'KHTN',
            'jfw' => 'JFW'
        ];
    }
}