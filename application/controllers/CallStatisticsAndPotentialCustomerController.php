<?php
/**
 * Created by PhpStorm.
 * User: phuocnguyen
 * Date: 10/09/2018
 * Time: 8:40 PM
 */

namespace application\controllers;

use application\models\CallStatisticsAndPotentialCustomer\CallStatisticsAndPotentialCustomer;
use application\models\Department\DepartmentUtil;
use application\models\User\UserUtil;
use application\utilities\DatetimeUtils;
use application\utilities\PermissionUtil;
use application\utilities\SessionUtils;
use yii\web\Controller;
use Yii;

class CallStatisticsAndPotentialCustomerController extends Controller
{
    const CONTROLLER = 'call-statistics-and-potential-customer';

    public function beforeAction($action)
    {
        PermissionUtil::canAccess(CallStatisticsAndPotentialCustomerController::CONTROLLER);
        return parent::beforeAction($action);
    }

    public function actionIndex()
    {
        $search = new CallStatisticsAndPotentialCustomer();
        $params = Yii::$app->request->get();
        $from_date = DatetimeUtils::getFirstDayOfMonthDateDependOnDevice();
        $to_date = DatetimeUtils::getCurrentDateDependOnDevice();
        $department = "";
        if(PermissionUtil::isXPMRole()){
            $department = SessionUtils::getDepartment()->id;
        }else {
            $departments = DepartmentUtil::getDepartments();
            $department = $departments[0]->id;

            if (isset($params['CallStatisticsAndPotentialCustomer'])) {
                $department = isset($params['CallStatisticsAndPotentialCustomer']['department']) ? $params['CallStatisticsAndPotentialCustomer']['department'] : $department;
            }
        }
        if (isset($params['CallStatisticsAndPotentialCustomer'])) {
            $from_date = isset($params['CallStatisticsAndPotentialCustomer']['from_date']) ? $params['CallStatisticsAndPotentialCustomer']['from_date'] : $from_date;
        }
        if (isset($params['CallStatisticsAndPotentialCustomer'])) {
            $to_date = isset($params['CallStatisticsAndPotentialCustomer']['to_date']) ? $params['CallStatisticsAndPotentialCustomer']['to_date'] : $to_date;
        }
        $search->from_date = $from_date;
        $search->to_date = $to_date;
        $search->department = $department;

        return $this->render('index', [
            'from_date' => $from_date,
            'to_date' => $to_date,
            'department' => $department,
            'model' => $search,
            'departments' => DepartmentUtil::getDropdownList(false)
        ]);
    }

}