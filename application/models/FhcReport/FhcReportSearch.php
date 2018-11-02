<?php
/**
 * Created by PhpStorm.
 * User: Tam
 * Date: 9/6/2018
 * Time: 9:21 PM
 */
namespace application\models\FhcReport;
use application\models\User\UserUtil;
use application\utilities\DatetimeUtils;
use application\utilities\PermissionUtil;
use application\utilities\SessionUtils;
use yii\data\ActiveDataProvider;

class FhcReportSearch extends FhcReport
{
    public $from_date;
    public $to_date;
    public $department_id;
    public function rules()
    {
        return [
            ['customer_id','trim'],
            ['user_id','integer'],
            ['from_date', 'trim'],
            ['to_date', 'trim'],
            ['department_id', 'trim'],
        ];
    }

    public function search($params, $isExportExcel=false)
    {
        $query = FhcReport::find()
            ->join('left join', 'customer', 'customer_id=customer.id')
            ->select("fhc_report.*, customer.name,customer.phone")
            ->orderBy(["fhc_report.created_at" => SORT_DESC]);
        if(PermissionUtil::isXPRole()){
            $query->andWhere(['fhc_report.user_id' => SessionUtils::getUserId()]);
        }
        if (PermissionUtil::isXPMRole()) {
            $users = UserUtil::getUserIdByDepartment();
            $query->where(['in', 'fhc_report.user_id' ,$users]);
        }
        if (PermissionUtil::isXCRole()) {
            $users = UserUtil::getUserIdByXcRole();
            $query->where(['in', 'fhc_report.user_id' ,$users]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $isExportExcel ? 10000 : \Yii::$app->params['page_size'],
            ],
        ]);

        $this->load($params);
        $from_date = date('01/m/Y');
        $to_date = date('d/m/Y');

        if(!empty($this->from_date)){
            $from_date = DatetimeUtils::convertStringToDate($this->from_date);
        }
        if(!empty($this->to_date)){
            $to_date = DatetimeUtils::convertStringToDateTime($this->to_date, 23, 59);
        }

        $query->andWhere(" fhc_report.date between '{$from_date}' and '{$to_date}'");

        if(!empty($this->customer_id)){
            $query->andFilterWhere(['like', 'customer.name', $this->customer_id]);
            $query->orFilterWhere(['like', 'customer.phone', $this->customer_id]);
        }
        if(!empty($this->department_id)){
            $sql_in ="select id from user where is_active=1 and department_id = '{$this->department_id}'";
            $query->andWhere(" fhc_report.user_id in ($sql_in)");
        }
        if(!empty($this->user_id)){
            $query->andFilterWhere(['=', 'fhc_report.user_id', $this->user_id]);
        }

        return $dataProvider;
    }
}