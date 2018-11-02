<?php

namespace application\models\MaritalStatus;
use yii\data\ActiveDataProvider;

class MaritalStatusSearch extends MaritalStatus
{
    public function rules()
    {
        return [
            ['name','trim']
        ];
    }

    public function search($params)
    {

        $query = MaritalStatus::find()->orderBy(["created_at" => SORT_DESC]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => \Yii::$app->params['page_size'],
            ],
        ]);



        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        if(!empty($this->name)){
            $query->andFilterWhere(['like','name',$this->name]);
        }
        return $dataProvider;
    }
}
