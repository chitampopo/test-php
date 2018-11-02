<?php

namespace application\models\PersonalSchedule;

use application\models\User\UserUtil;
use application\utilities\DatetimeUtils;
use application\utilities\PermissionUtil;
use application\utilities\SessionUtils;
use yii\data\ActiveDataProvider;

class PersonalScheduleSearch extends PersonalSchedule
{
    public function rules()
    {
        return [
            ['customer_id', 'trim'],
            ['date', 'trim'],
            ['user_id','trim'],
            ['completed','trim']
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
        $user_id = SessionUtils::getUserId();
        $query = PersonalSchedule::find()
            ->join('left join', 'customer', 'customer_id=customer.id')
            ->select("personal_schedule.*, customer.name");
        if(!PermissionUtil::isXPRole()){
            $query->andWhere("personal_schedule.id in (SELECT id FROM 
                        personal_schedule
                        WHERE user_id = '{$user_id}'
                        UNION ALL
                        SELECT xp_schedule_id FROM 
                        jfw_schedule
                        WHERE user_id = '{$user_id}')");
        }else {
            $query->andWhere(['personal_schedule.user_id' => $user_id]);
        }
        $query->orderBy(["personal_schedule.date" => SORT_ASC]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => \Yii::$app->params['page_size'],
            ],
        ]);

        $this->load($params);
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