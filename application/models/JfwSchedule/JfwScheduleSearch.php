<?php

namespace application\models\JfwSchedule;

use application\models\PersonalSchedule\PersonalSchedule;
use application\models\PersonalSchedule\PersonalScheduleSearch;
use application\models\User\UserUtil;
use application\utilities\DatetimeUtils;
use application\utilities\PermissionUtil;
use application\utilities\SessionUtils;
use yii\data\ActiveDataProvider;

class JfwScheduleSearch extends PersonalScheduleSearch
{
    public $department_id;

    public function rules()
    {
        return [
            ['customer_id', 'trim'],
            ['date', 'trim'],
            ['user_id','trim'],
            ['completed','trim'],
            ['department_id', 'trim'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'customer_id' => 'Tên/SĐT',
            'date' => 'Ngày',
            'user_id' =>'Nhân viên',
            'completed' => 'Trạng thái'
        ];
    }

    public function search($params)
    {
        $users = UserUtil::getUserIdByDepartment();
        $query = PersonalSchedule::find()
            ->join('left join', 'customer', 'customer_id=customer.id')
            ->select("personal_schedule.*, customer.name")
            ->where(['in', 'personal_schedule.user_id' ,$users]);
        $query->orderBy(["personal_schedule.date" => SORT_ASC]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => \Yii::$app->params['page_size'],
            ],
        ]);

        $this->load($params);
        if(!empty($this->department_id)){
            $sql_in ="select id from user where is_active=1 and department_id = '{$this->department_id}'";
            $query->andWhere(" personal_schedule.user_id in ($sql_in)");
        }
        if (!empty($this->user_id)) {
            $query->andWhere(['personal_schedule.user_id' => $this->user_id]);
        }

        if (!empty($this->customer_id)) {
            $query->andWhere(['like', 'customer.name', $this->customer_id]);
        }
        if (!empty($this->date)) {
            $query->andWhere(["date_format(date,'%Y-%m-%d')" => DatetimeUtils::convertStringToDate($this->date)]);
        } else {
            $query->andWhere(["date_format(date,'%Y-%m-%d')" => date('Y-m-d')]);
        }

        if (!empty($this->completed) || $this->completed == "0") {
            $query->andWhere(['personal_schedule.completed' => $this->completed]);
        }
        
        return $dataProvider;
    }
}