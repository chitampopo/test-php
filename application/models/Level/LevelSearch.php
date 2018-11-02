<?php
/**
 * Created by PhpStorm.
 * User: phuocnguyen
 * Date: 04/09/2018
 * Time: 11:39 PM
 */

namespace application\models\Level;


use yii\data\ActiveDataProvider;

class LevelSearch extends Level
{
    public function rules()
    {
        return [
            ['name','trim']
        ];
    }

    public function search($params)
    {

        $query = Level::find()->orderBy(["created_at" => SORT_DESC]);
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