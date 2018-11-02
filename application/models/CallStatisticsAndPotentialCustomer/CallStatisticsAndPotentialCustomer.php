<?php
/**
 * Created by PhpStorm.
 * User: phuocnguyen
 * Date: 10/09/2018
 * Time: 8:43 PM
 */
namespace application\models\CallStatisticsAndPotentialCustomer;
use application\models\Chanel\Chanel;
use application\models\Chanel\ChanelUtil;
use yii\base\Model;
use yii\db\ActiveRecord;
use Yii;
class CallStatisticsAndPotentialCustomer extends Model
{
    public $from_date;
    public $to_date;
    public $department;
    public function rules()
    {
        $rule = [
            ['from_date', 'trim'],
            ['to_date', 'trim'],
            ['department','trim']
        ];
        return $rule;
    }

    public function attributeLabels()
    {
        return [
            'from_date' => 'Từ ngày',
            'to_date' => 'Đến ngày',
            'department'=>'Phòng'
        ];
    }
    public function buildQuery($from_date, $to_date, $userid){

        $array = array(
            'goi',
            'gap',
            'khtn',
            'fhc',
            'sis',
            'hd'
        );
        $sql = "";
        foreach ($array as $index => $item) {
            $chanels = ChanelUtil::getChanels();
            $sql.="SELECT ";
            if($item=='goi') {
                foreach ($chanels as $index => $chanel) {
                    $sql .= "(
                SELECT 
                COUNT(id) AS number
                FROM call_result
                WHERE user_id ='{$userid}' and call_date between '{$from_date}' and '{$to_date}' AND chanel_id = '{$chanel->id}'
                ) AS chanel_" . $chanel->id . ",";
                }
            } else if($item=='khtn'){
                foreach ($chanels as $index => $chanel) {
                    $sql .= "(
                    SELECT 
                    IFNULL(count(potential_customers.id),0) AS number
                    FROM potential_customers
                    WHERE user_id ='{$userid}' and potential_customers.date between '{$from_date}' and '{$to_date}' AND chanel_id = '{$chanel->id}'
                    ) AS chanel_" . $chanel->id . ",";
                }
            } else if($item=='gap'){
                foreach ($chanels as $index => $chanel) {
                    $sql .= "(
                    SELECT 
                    COUNT(id) AS number
                    FROM meeting_result
                    WHERE user_id ='{$userid}' and meeting_date between '{$from_date}' and '{$to_date}' AND chanel_id = '{$chanel->id}'
                    ) AS chanel_" . $chanel->id . ",";
                }

            }else{
                foreach ($chanels as $index => $chanel) {
                    $sql .= "(
                    SELECT 
                    COUNT(id) AS number
                    FROM meeting_result
                    WHERE user_id ='{$userid}' and meeting_date between '{$from_date}' and '{$to_date}' AND chanel_id = '{$chanel->id}' and {$item}=1
                    ) AS chanel_" . $chanel->id . ",";
                }
            }
            $sql = substr($sql, 0, -1);
            $sql .= " union all ";
        }
        return substr($sql,0,-10);
    }
    public function buildQueryForDepartment($from_date, $to_date, $department_id){
        $array = array(
            'goi',
            'gap',
            'khtn',
            'fhc',
            'sis',
            'hd'
        );
        $sql = "";
        foreach ($array as $index => $item) {
            $chanels = ChanelUtil::getChanels();
            $sql.="SELECT ";
            if($item=='goi') {
                foreach ($chanels as $index => $chanel) {
                    $sql .= "(
                SELECT 
                COUNT(call_result.id) AS number
                FROM call_result join user on call_result.user_id = user.id
                WHERE user.department_id ='{$department_id}' and call_date between '{$from_date}' and '{$to_date}' AND chanel_id = '{$chanel->id}'
                ) AS chanel_" . $chanel->id . ",";
                }
            } else if($item=='khtn'){
                foreach ($chanels as $index => $chanel) {
                    $sql .= "(
                    SELECT 
                    IFNULL(count(potential_customers.id),0) AS number
                    FROM potential_customers join user on potential_customers.user_id = user.id
                    WHERE user.department_id ='{$department_id}' and potential_customers.date between '{$from_date}' and '{$to_date}' AND chanel_id = '{$chanel->id}'
                    ) AS chanel_" . $chanel->id . ",";
                }
            } else if($item=='gap'){
                foreach ($chanels as $index => $chanel) {
                    $sql .= "(
                    SELECT 
                    COUNT(meeting_result.id) AS number
                    FROM meeting_result join user on meeting_result.user_id = user.id
                    WHERE user.department_id ='{$department_id}' and meeting_date between '{$from_date}' and '{$to_date}' AND chanel_id = '{$chanel->id}'
                    ) AS chanel_" . $chanel->id . ",";
                }

            }else{
                foreach ($chanels as $index => $chanel) {
                    $sql .= "(
                    SELECT 
                    COUNT(meeting_result.id) AS number
                    FROM meeting_result join user on meeting_result.user_id = user.id
                    WHERE user.department_id ='{$department_id}' and meeting_date between '{$from_date}' and '{$to_date}' AND chanel_id = '{$chanel->id}' and {$item}=1
                    ) AS chanel_" . $chanel->id . ",";
                }
            }
            $sql = substr($sql, 0, -1);
            $sql .= " union all ";
        }
        return substr($sql,0,-10);
    }

}