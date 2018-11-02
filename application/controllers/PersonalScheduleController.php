<?php

namespace application\controllers;

use application\models\PersonalSchedule\PersonalSchedule;
use application\models\PersonalSchedule\PersonalScheduleSearch;
use application\models\PersonalSchedule\PersonalScheduleUtil;
use application\models\JfwSchedule\JfwSchedule;
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
use application\utilities\PersonalScheduleUrlUtils;
use application\utilities\SessionUtils;
use application\utilities\UrlUtils;
use Yii;
use yii\web\Controller;

class PersonalScheduleController extends Controller
{
    public function beforeAction($action)
    {
        PermissionUtil::canAccess('personal-schedule');
        return parent::beforeAction($action);
    }

    public function actionIndex()
    {
        $personalScheduleSearch = new PersonalScheduleSearch();
        $personalScheduleSearch->date = DatetimeUtils::getCurrentDateDependOnDevice();
        $personalScheduleSearch->completed = 0;
        $params = Yii::$app->request->get();
        $data = $personalScheduleSearch->search($params);
        return $this->render('index', [
            'data' => $data,
            'personalScheduleSearch' => $personalScheduleSearch,
            'completedStatus' => PersonalScheduleUtil::getScheduleCompletedDropdownList(false),
            'customers' => CustomerUtil::getDropdownList(false),
            'chanels' => ChanelUtil::getDropdownList(false),
            'purposes' => PurposeUtil::getDropdownList(false)
        ]);
    }

    public function actionUpdate($id = null)
    {
        $personalSchedule = new PersonalSchedule();
        $isHaveAnError = false;
        if (UrlUtils::isEditAction($id)) {
            $personalSchedule = PersonalSchedule::findOne(['id' => $id]);
        }
        $post = Yii::$app->request->post();
        if ($personalSchedule->load($post)) {
            $postPersonalSchedule = $post['PersonalSchedule'];
            $personalSchedule->is_new_customer = isset($postPersonalSchedule['is_new_customer']) ? $postPersonalSchedule['is_new_customer'] : 0;
            $personalSchedule->fhc = isset($postPersonalSchedule['fhc']) ? $postPersonalSchedule['fhc'] : 0;
            $personalSchedule->sis = isset($postPersonalSchedule['sis']) ? $postPersonalSchedule['sis'] : 0;
            $personalSchedule->xuly = isset($postPersonalSchedule['xuly']) ? $postPersonalSchedule['xuly'] : 0;
            $personalSchedule->is_call = isset($postPersonalSchedule['is_call']) ? $postPersonalSchedule['is_call'] : 0;
            $personalSchedule->completed = isset($postPersonalSchedule['completed']) ? $postPersonalSchedule['completed'] : 0;
            $personalSchedule->hour = isset($postPersonalSchedule['hour']) ? $postPersonalSchedule['hour'] : 0;
            $personalSchedule->minute = isset($postPersonalSchedule['minute']) ? $postPersonalSchedule['minute'] : 0;

            $date = "";
            if (!empty($personalSchedule->date)) {
                $date = DatetimeUtils::convertStringToDateTime($personalSchedule->date, $personalSchedule->hour, $personalSchedule->minute);
            }
            $personalScheduleByDatetime = PersonalScheduleUrlUtils::getPersonalScheduleByDateTime($date, $id);
            if (count($personalScheduleByDatetime) > 0 && $personalSchedule->completed==0) {
                $listDuplicateSchedule = "";
                foreach ($personalScheduleByDatetime as $index => $item) {
                    $customerHaveSchedule = Customer::findOne(['id' => $item->customer_id]);
                    $customer_name = "";
                    if (!is_null($customerHaveSchedule)) {
                        $customer_name = $customerHaveSchedule->name;
                    }
                    $listDuplicateSchedule .= $customer_name . " (" . DatetimeUtils::formatDate($item->date, "d/m/Y H:i") . ") ";
                }
                $isHaveAnError = true;
                MessageUtils::showMessage(false, "Bạn đã có lịch hẹn {$listDuplicateSchedule}, vui lòng chọn thời gian khác");
            } else {
                $result = $this->updateData($personalSchedule, $id);
                MessageUtils::showMessage($result);
                if (!UrlUtils::isEditAction($id)) {
                    $personalSchedule = new PersonalSchedule();
                }
            }
        }

        if(!$isHaveAnError) {
            if (UrlUtils::isEditAction($id)) {
                $formatDate = "d/m/Y";
                if (DetectDeviceUtil::isMobile()) {
                    $formatDate = "Y-m-d";
                }
                if (DatetimeUtils::isDatetimeNotEmptyOrNull($personalSchedule->date)) {
                    $personalSchedule->hour = DatetimeUtils::formatDate($personalSchedule->date, 'H');
                    $personalSchedule->minute = DatetimeUtils::formatDate($personalSchedule->date, 'i');
                    $personalSchedule->date = DatetimeUtils::formatDate($personalSchedule->date, $formatDate);
                }
                if (DatetimeUtils::isDatetimeNotEmptyOrNull($personalSchedule->completed_date)) {
                    $personalSchedule->completed_date = DatetimeUtils::formatDate($personalSchedule->completed_date, $formatDate);
                } else {
                    $personalSchedule->completed_date = "";
                }
            } else {
                $get = Yii::$app->request->get();
                if(isset($get["customer"])){
                    $personalSchedule->customer_id = $get["customer"];
                }
                $personalSchedule->date = DatetimeUtils::getCurrentDateDependOnDevice();
                $personalSchedule->completed_date = "";

            }
        }
        return $this->render('update', [
            'model' => $personalSchedule,
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

            if(!PermissionUtil::isXPRole()){
                $params[]='fhc';
                $params[]='sis';
                $params[]='xuly';
                $params[]='referral';
                $params[]='recruiment';
                $params[]='other';
            }

            $model->updated_by = SessionUtils::getUsername();
            $model->updated_at = date('Y-m-d H:i:s');
            return $model->save(true, $params);
        }
        return $model->save();
    }

    public function actionDelete()
    {
        $post = Yii::$app->request->post();
        $values = null;
        if (isset($post)) {
            $values = isset($post["values"]) ? $post["values"] : null;
        }
        if (!is_null($values)) {
            if (!PermissionUtil::isXpRole()) {
                $remainings = $values;
                foreach ($remainings as $id) { 
                    $where = array('xp_schedule_id' => $id, 'user_id' => SessionUtils::getUserId());
                    $object = new JfwSchedule();
                    $result = $object::deleteAll($where);
                    if ($result > 0) {
                    $values = array_diff($values, array($id));
                    }
                }
                if (empty($values)) {
                    return 1;
                }
            }
            $where = array('id' => $values);
            $object = new PersonalSchedule();
            $result = $object::deleteAll($where);
            if ($result > 0) {
                return 1;
            } else {
                return 0;
            }
        } else {
            return -1;
        }
    }

    public function actionUpdateComplete() {
        $post = Yii::$app->request->post();
        if (isset($post)) {
            if (isset($post['scheduleId']) && isset($post['isComplete'])) {
                $personalSchedule = PersonalSchedule::findOne(['id' => $post['scheduleId']]);
                $personalSchedule->completed = $post['isComplete'];
                if ($personalSchedule->completed == 1) {
                    $personalSchedule->completed_date = DatetimeUtils::convertStringToDate(date('d/m/Y'));
                } else {
                    $personalSchedule->completed_date = null;
                }
                if($personalSchedule->save()){
                    return DatetimeUtils::isDatetimeNotEmptyOrNull($personalSchedule->completed_date) ? DatetimeUtils::formatDate($personalSchedule->completed_date):"";
                }
            }
        }
    }
}