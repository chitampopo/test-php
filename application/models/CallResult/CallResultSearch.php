<?php
/**
 * Created by PhpStorm.
 * User: phuocnguyen
 * Date: 06/09/2018
 * Time: 9:04 PM
 */

namespace application\models\CallResult;


use application\models\User\UserUtil;
use application\utilities\DatetimeUtils;
use application\utilities\PermissionUtil;
use application\utilities\SessionUtils;
use yii\data\ActiveDataProvider;

class CallResultSearch extends CallResult
{
    public $name;
    public $department_id;
    public $from_date;
    public function rules()
    {
        return [
            ['customer_id', 'trim'],
            ['call_date', 'trim'],
            ['user_id','trim'],
            ['department_id','trim'],
            ['from_date','trim']
        ];
    }

    public function attributeLabels()
    {
        return [
            'customer_id' => 'Tên/SĐT',
            'call_date' =>'Đến ngày',
            'user_id' =>'Nhân viên',
            'department_id' =>'Phòng',
            'from_date' => 'Từ ngày'
        ];
    }

    public function search($params)
    {
        $query = CallResult::find()
            ->join('left join', 'customer', 'customer_id=customer.id')
            ->select("call_result.*, customer.name,customer.phone");
        if (PermissionUtil::isXPRole()) {
            $query->andWhere(['call_result.user_id' => SessionUtils::getUserId()]);
        }
        if(PermissionUtil::isXPMRole()){
            $users = UserUtil::getUserIdByDepartment();
            $query->where(['in', 'call_result.user_id' ,$users]);
        }
        if(PermissionUtil::isXCRole()){
            $users = UserUtil::getUserIdByXcRole();
            $query->where(['in', 'call_result.user_id' ,$users]);
        }
        $query->orderBy(["call_result.created_at" => SORT_DESC]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => \Yii::$app->params['page_size'],
            ],
        ]);

        $this->load($params);
        if(!empty($this->department_id)){
            $sql_in ="select id from user where is_active=1 and department_id = '{$this->department_id}'";
            $query->andWhere(" call_result.user_id in ($sql_in)");
        }
        if (!empty($this->user_id)) {
            $query->andWhere(['call_result.user_id' => $this->user_id]);
        }
        if (!empty($this->customer_id)) {
            $query->andFilterWhere(['like', 'customer.name', $this->customer_id]);
            $query->orFilterWhere(['like', 'customer.phone', $this->customer_id]);
        }
        if (!empty($this->call_date)) {
            //$query->andWhere(["date_format(call_date,'%Y-%m-%d')" => DatetimeUtils::convertStringToDate($this->call_date)]);
            $query->andWhere(['between', 'call_date', 
                DatetimeUtils::convertStringToDate($this->from_date), 
                DatetimeUtils::convertStringToDate($this->call_date)]);
        } else {
            $query->andWhere(["date_format(call_date,'%Y-%m-%d')" => date('Y-m-d')]);
        }
        return $dataProvider;
    }
}