<?php

namespace application\models\User;
use yii\data\ActiveDataProvider;

class UserInfoSearch extends UserInfo
{
    public $keyword;

    public function rules()
    {
        return [
            ['keyword','trim'],
            ['name','trim'],
            ['username', 'trim'],
            ['phone', 'trim'],
            ['email', 'trim'],
            ['address', 'trim'],
            ['level_id','trim'],
            ['department_id','trim'],
            ['is_active','trim']
        ];
    }

    public function search($params)
    {
        $query = UserInfo::find()
            ->andWhere('level_id <> 4')
            ->orderBy(["created_at" => SORT_DESC]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => \Yii::$app->params['page_size'],
            ],
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }
        if(!empty($this->keyword)){
            $query->andFilterWhere(['or',
                ['like','name', $this->keyword],
                ['like','username', $this->keyword],
                ['like','phone', $this->keyword],
                ['like','email', $this->keyword],
                ['like','address', $this->keyword]]);
        }

        if(!empty($this->level_id)){
            $query->andFilterWhere(['=', 'level_id', $this->level_id]);
        }

        if(!empty($this->department_id)){
            $query->andFilterWhere(['=', 'department_id', $this->department_id]);
        }

        return $dataProvider;
    }

    public function attributeLabels()
    {
        return [
            'keyword' => 'Từ khóa'
        ];
    }
}
