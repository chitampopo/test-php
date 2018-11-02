<?php
/**
 * Created by PhpStorm.
 * User: phuocnguyen
 * Date: 05/09/2018
 * Time: 10:09 PM
 */

namespace application\models\Purpose;
use yii\data\ActiveDataProvider;

class PurposeSearch extends Purpose
{
    public function rules()
    {
        return [
            ['name','trim']
        ];
    }

    public function search($params)
    {
        $query = Purpose::find()->orderBy(["created_at" => SORT_DESC]);
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