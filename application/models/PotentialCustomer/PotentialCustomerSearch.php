<?php
/**
 * Created by PhpStorm.
 * User: phuocnguyen
 * Date: 09/09/2018
 * Time: 4:03 PM
 */

namespace application\models\PotentialCustomer;


use application\models\User\UserUtil;
use application\utilities\DatetimeUtils;
use application\utilities\PermissionUtil;
use application\utilities\SessionUtils;
use yii\data\ActiveDataProvider;

class PotentialCustomerSearch extends PotentialCustomer
{

    public $department_id;

    public function rules()
    {
        return [
            ['customer_id', 'trim'],
            ['date', 'trim'],
            ['user_id', 'trim'],
            ['department_id', 'trim'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'customer_id' => 'Tên/SĐT',
            'date' => 'Ngày',
            'user_id' => 'Nhân viên'
        ];
    }

    public function search($params, $isExportExcel = false)
    {
        $query = PotentialCustomer::find()
            ->join('left join', 'customer', 'customer_id=customer.id')
            ->select("potential_customers.*, customer.name,customer.phone");
        if (PermissionUtil::isXPRole()) {
            $query->andWhere(['potential_customers.user_id' => SessionUtils::getUserId()]);
        }
        if (PermissionUtil::isXPMRole()) {
            $users = UserUtil::getUserIdByDepartment();
            $query->where(['in', 'potential_customers.user_id', $users]);
        }
        if (PermissionUtil::isXCRole()) {
            $users = UserUtil::getUserIdByXcRole();
            $query->where(['in', 'potential_customers.user_id', $users]);
        }
        $query->orderBy(["potential_customers.user_id" => SORT_ASC]);
        $query->orderBy(["customer.name1" => SORT_ASC]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $isExportExcel ? 10000 : \Yii::$app->params['page_size'],
            ],
        ]);
        $this->load($params);
        if(!empty($this->department_id)){
            $sql_in ="select id from user where is_active=1 and department_id = '{$this->department_id}'";
            $query->andWhere(" potential_customers.user_id in ($sql_in)");
        }
        if (!empty($this->user_id)) {
            $query->andWhere(['potential_customers.user_id' => $this->user_id]);
        }

        if (!empty($this->customer_id)) {
            $query->andFilterWhere(['like', 'customer.name', $this->customer_id]);
            $query->orFilterWhere(['like', 'customer.phone', $this->customer_id]);
        }
        if (!empty($this->date)) {
            $query->andWhere(["date_format(date,'%Y-%m-%d')" => DatetimeUtils::convertStringToDate($this->date)]);
        } else {
            $query->andWhere(["date_format(date,'%Y-%m-%d')" => date('Y-m-d')]);
        }

        return $dataProvider;
    }
}