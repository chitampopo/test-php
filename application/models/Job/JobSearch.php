<?php
/**
 * Created by PhpStorm.
 * User: phuocnguyen
 * Date: 29/09/2018
 * Time: 3:12 PM
 */

namespace application\models\Job;


use yii\data\ActiveDataProvider;

class JobSearch extends Job
{
    public function rules()
    {
        return [
            ['name','trim']
        ];
    }

    public function search($params)
    {
        $query = Job::find()->orderBy(["created_at" => SORT_DESC]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => \Yii::$app->params['page_size'],
            ],
        ]);
        $this->load($params);
        if(!empty($this->name)){
            $query->andFilterWhere(['like','name',$this->name]);
        }
        return $dataProvider;
    }
}