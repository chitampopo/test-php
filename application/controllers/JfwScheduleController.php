<?php

namespace application\controllers;

use application\models\PersonalSchedule\PersonalSchedule;
use application\models\PersonalSchedule\PersonalScheduleSearch;
use application\models\PersonalSchedule\PersonalScheduleUtil;
use application\models\JfwSchedule\JfwSchedule;
use application\models\JfwSchedule\JfwScheduleSearch;
use application\models\Chanel\ChanelUtil;
use application\models\Customer\Customer;
use application\models\Customer\CustomerUtil;
use application\models\Purpose\PurposeUtil;
use application\models\User\UserUtil;
use application\utilities\DatetimeUtils;
use application\utilities\DeleteDataUtil;
use application\utilities\DetectDeviceUtil;
use application\utilities\MessageUtils;
use application\utilities\PermissionUtil;
use application\utilities\SessionUtils;
use application\utilities\UrlUtils;
use Yii;
use yii\web\Controller;

class JfwScheduleController extends Controller
{
    public function beforeAction($action)
    {
        PermissionUtil::canAccess('jfw-schedule');
        return parent::beforeAction($action);
    }

    public function actionIndex()
    {
        $jfwScheduleSearch = new JfwScheduleSearch();
        $jfwScheduleSearch->date = DatetimeUtils::getCurrentDateDependOnDevice();
        $jfwScheduleSearch->completed = 0;
        $params = Yii::$app->request->get();
        $data = $jfwScheduleSearch->search($params);
        $users = UserUtil::getDropdownList(false);
        if(!PermissionUtil::isXPMRole() && !PermissionUtil::isXPRole()) {
            if (isset($params["JfwScheduleSearch"])) {
                $getFhcReportSearch = $params["JfwScheduleSearch"];
                $department_id = isset($getFhcReportSearch["department_id"]) ? $getFhcReportSearch["department_id"] : "";
                if(!empty($department_id)) {
                    $users = UserUtil::getDropdownListRelatedToUsers(false, UserUtil::getUserByDepartment($department_id));
                }
            }
        }

        return $this->render('index', [
            'data' => $data,
            'jfwScheduleSearch' => $jfwScheduleSearch,
            'completedStatus' => PersonalScheduleUtil::getScheduleCompletedDropdownList(false),
            'customers' => CustomerUtil::getDropdownList(false),
            'users' => $users,
            'chanels' => ChanelUtil::getDropdownList(false),
            'purposes' => PurposeUtil::getDropdownList(false)
        ]);
    }

    public function actionUpdate($id = null)
    {
        $jfwSchedule = new PersonalSchedule();
        if (UrlUtils::isEditAction($id)) {
            $jfwSchedule = PersonalSchedule::findOne(['id' => $id]);
        }
        $post = Yii::$app->request->post();
        if ($jfwSchedule->load($post)) {
            $jfwSchedulePost = $post['JfwSchedule'];
            $jfwSchedule->is_new_customer = isset($jfwSchedulePost['is_new_customer']) ? $jfwSchedulePost['is_new_customer'] : 0;
            $jfwSchedule->is_call = isset($jfwSchedulePost['is_call']) ? $jfwSchedulePost['is_call'] : 0;
            $jfwSchedule->completed = isset($jfwSchedulePost['completed']) ? $jfwSchedulePost['completed'] : 0;
            $jfwSchedule->hour = isset($jfwSchedulePost['hour']) ? $jfwSchedulePost['hour'] : 0;
            $jfwSchedule->minute = isset($jfwSchedulePost['minute']) ? $jfwSchedulePost['minute'] : 0;
            $result = $this->updateData($jfwSchedule, $id);
            MessageUtils::showMessage($result);
            if (!UrlUtils::isEditAction($id)) {
                $jfwSchedule = new PersonalSchedule();
            }
        }
        $formatDate = "d/m/Y";
        if(DetectDeviceUtil::isMobile()){
            $formatDate = "Y-m-d";
        }
        if (UrlUtils::isEditAction($id)) {
            if(DatetimeUtils::isDatetimeNotEmptyOrNull($jfwSchedule->date)) {
                $jfwSchedule->hour = DatetimeUtils::formatDate($jfwSchedule->date, 'H');
                $jfwSchedule->minute = DatetimeUtils::formatDate($jfwSchedule->date, 'i');
                $jfwSchedule->date = DatetimeUtils::formatDate($jfwSchedule->date, $formatDate);
            }
            $jfwSchedule->completed_date = DatetimeUtils::isDatetimeNotEmptyOrNull($jfwSchedule->completed_date) ? DatetimeUtils::formatDate($jfwSchedule->completed_date, $formatDate) : "";
        } else {
            $jfwSchedule->date = DatetimeUtils::getCurrentDateDependOnDevice();
            $jfwSchedule->completed_date = "";

        }
        return $this->render('update', [
            'model' => $jfwSchedule,
            'chanels' => ChanelUtil::getDropdownList(false),
            'purposes' => PurposeUtil::getDropdownList(false),
            'customers' => CustomerUtil::getDropdownList(false),
            'users' => UserUtil::getDropdownList(false)
        ]);
    }

    private function updateData($model, $id)
    {
        if (!empty($model->date)) {
            $model->date = DatetimeUtils::convertStringToDateTime($model->date, $model->hour, $model->minute);
        } else {
            $model->date = null;
        }

        if (!empty($model->completed_date)) {
            $model->completed_date = DatetimeUtils::convertStringToDate($model->completed_date);
        } else {
            $model->completed_date = null;
        }
        if (UrlUtils::isEditAction($id)) {
            $params = [
                'customer_id',
                'is_new_customer',
                'chanel_id',
                'date',
                'purpose_id',
                'is_call',
                'completed',
                'completed_date'
            ];

            $model->updated_by = SessionUtils::getUsername();
            $model->updated_at = date('Y-m-d H:i:s');
            return $model->save(true, $params);
        }
        return $model->save();
    }

    public function actionJfwXp() {
        $post = Yii::$app->request->post();
        if (isset($post)) {
            if (isset($post['xpScheduleId']) && isset($post['isJfw'])) {
                if ($post['isJfw']) {
                    $jfwSchedule = new JfwSchedule();
                    $jfwSchedule->xp_schedule_id = $post['xpScheduleId'];
                    $jfwSchedule->user_id = SessionUtils::getUserId();
                    return $jfwSchedule->save();
                } else {
                    return JfwSchedule::deleteAll(["xp_schedule_id" => $post['xpScheduleId'], "user_id" => SessionUtils::getUserId()]);
                }
            }
        }
    }
}