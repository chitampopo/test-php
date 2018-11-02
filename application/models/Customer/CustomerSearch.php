<?php
/**
 * Created by PhpStorm.
 * User: Tam
 * Date: 9/6/2018
 * Time: 9:21 PM
 */

namespace application\models\Customer;

use application\models\User\User;
use application\models\User\UserUtil;
use application\utilities\PermissionUtil;
use application\utilities\QueryUtil;
use application\utilities\SessionUtils;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

class CustomerSearch extends Customer
{
    public $department_id;
    public $categories;
    public function rules()
    {
        return [
            ['name', 'trim'],
            ['chanel_id', 'integer'],
            ['user_id', 'integer'],
            ['department_id', 'trim'],
            ['categories', 'trim'],
        ];
    }

    public function search($params, $isExportExcel = false)
    {
        $query = CustomerUtil::buildQueryGetCustomers();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $isExportExcel? 10000 : \Yii::$app->params['page_size'],
            ],
        ]);

        $this->load($params);

        if (!empty($this->name)) {
            $query->andFilterWhere(['or',
                ['like', 'name', $this->name],
                ['like', 'phone', $this->name]
            ]);
        }

        if (!empty($this->chanel_id)) {
            $query->andFilterWhere(['=', 'chanel_id', $this->chanel_id]);
        }
        if (!empty($this->department_id)) {
            $sql = QueryUtil::getQuerySelectUserIdInDepartment($this->department_id);
            $query->andWhere(" user_id in ($sql)");
        }
        if (!empty($this->user_id)) {
            $query->andFilterWhere(['=', 'user_id', $this->user_id]);
        }
        if (!empty($this->categories)) {
            $query->andFilterWhere(['=', 'category', $this->categories]);
        }

        return $dataProvider;
    }
}