<?php
/**
 * Created by PhpStorm.
 * User: phuocnguyen
 * Date: 05/09/2018
 * Time: 9:27 PM
 */

namespace application\models\Chanel;
use yii\data\ActiveDataProvider;

class ChanelSearch extends Chanel
{
    public function rules()
    {
        return [
            ['name','trim']
        ];
    }

    public function search($params)
    {
        $query = Chanel::find()->orderBy(["created_at" => SORT_DESC]);
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