<?php
/**
 * Created by PhpStorm.
 * User: phuocnguyen
 * Date: 06/09/2018
 * Time: 8:56 PM
 */

namespace application\controllers;

use application\models\CallResult\CallResult;
use application\models\CallResult\CallResultSearch;
use application\models\Chanel\ChanelUtil;
use application\models\Customer\Customer;
use application\models\Customer\CustomerUtil;
use application\models\PersonalSchedule\PersonalSchedule;
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

class CallResultController extends Controller
{
    public function beforeAction($action)
    {
        PermissionUtil::canAccess('call-result');
        return parent::beforeAction($action);
    }

    public function actionIndex()
    {
        $callResultSearch = new CallResultSearch();
        $callResultSearch->call_date = DatetimeUtils::getCurrentDateDependOnDevice();
        $callResultSearch->from_date = DatetimeUtils::getCurrentDateDependOnDevice();
        $params = Yii::$app->request->get();
        $data = $callResultSearch->search($params);
        $users = UserUtil::getDropdownList(false);
        if (!PermissionUtil::isXPMRole() && !PermissionUtil::isXPRole()) {
            if (isset($params["CallResultSearch"])) {
                $getFhcReportSearch = $params["CallResultSearch"];
                $department_id = isset($getFhcReportSearch["department_id"]) ? $getFhcReportSearch["department_id"] : "";
                if (!empty($department_id)) {
                    $users = UserUtil::getDropdownListRelatedToUsers(false, UserUtil::getUserByDepartment($department_id));
                }
            }
        }
        return $this->render('index', [
            'data' => $data,
            'callResultSearch' => $callResultSearch,
            'chanels' => ChanelUtil::getDropdownList(false),
            'purposes' => PurposeUtil::getDropdownList(false),
            'users' => $users

        ]);
    }

    public function actionUpdate($id = null)
    {
        $callResult = new CallResult();
        $personalSchedule = new PersonalSchedule();
        $isHaveAnError = false;
        if (UrlUtils::isEditAction($id)) {
            $callResult = CallResult::findOne(['id' => $id]);
            $personalSchedule = PersonalSchedule::findOne(['created_from' => CallResult::CALL_RESULT . '-' . $id]);
            if (!is_null($personalSchedule)) {
                $callResult->is_add_schedule = 1;
            }
        } else {
            $get = Yii::$app->request->get();
            if(isset($get["customer"])){
                $callResult->customer_id = $get["customer"];
            }
            if (isset($get['result-id'])) {
                $callResult->customer_id = !empty($get['result-id']) ? $get['result-id'] : null;
            } else if (isset($get['schedule-id']) && !empty($get['schedule-id'])) {
                $personalSchedule = PersonalSchedule::findOne(['id' => $get['schedule-id']]);
                if (!is_null($personalSchedule)) {
                    $callResult->customer_id = $personalSchedule->customer_id;
                    $callResult->chanel_id = $personalSchedule->chanel_id;
                    $callResult->purpose_id = $personalSchedule->purpose_id;
                    $callResult->schedule_id = $personalSchedule->id;
                }
            }
        }
        $post = Yii::$app->request->post();
        if ($callResult->load($post)) {
            $postCallResult = $post['CallResult'];
            $callResult->is_add_schedule = isset($postCallResult['is_add_schedule']) ? $postCallResult['is_add_schedule'] : 0;
            $callResult->hour = isset($postCallResult['hour']) ? $postCallResult['hour'] : 0;
            $callResult->minute = isset($postCallResult['minute']) ? $postCallResult['minute'] : 0;
            $callResult->schedule_id = isset($postCallResult['schedule_id']) ? $postCallResult['schedule_id'] : 0;

            $appointment_date = "";
            if (!empty($callResult->appointment_date)) {
                $appointment_date = DatetimeUtils::convertStringToDateTime($callResult->appointment_date, $callResult->hour, $callResult->minute);
            }
            $personalScheduleByDatetime = PersonalScheduleUrlUtils::getPersonalScheduleByDateTime($appointment_date, !is_null($personalSchedule)?$personalSchedule->id:"");
            if (count($personalScheduleByDatetime) > 0 && $callResult->is_add_schedule==1) {
                $listDuplicateSchedule = "";
                foreach ($personalScheduleByDatetime as $index => $item) {
                    $customer = Customer::findOne(['id'=>$item->customer_id]);
                    $customer_name ="";
                    if(!is_null($customer)){
                        $customer_name = $customer->name;
                    }
                    $listDuplicateSchedule.= $customer_name." (".DatetimeUtils::formatDate($item->date,"d/m/Y H:i").") ";
                }
                $isHaveAnError= true;
                MessageUtils::showMessage(false, "Bạn đã có lịch hẹn {$listDuplicateSchedule}, vui lòng chọn thời gian khác");
            } else {
                $result = $this->updateData($callResult, $id);
                MessageUtils::showMessage($result);
                if (!UrlUtils::isEditAction($id)) {
                    $callResult = new CallResult();
                }
            }
        }
        if(!$isHaveAnError) {
            if (UrlUtils::isEditAction($id)) {
                $formatDate = "d/m/Y";
                if (DetectDeviceUtil::isMobile()) {
                    $formatDate = "Y-m-d";
                }
                $callResult->call_date = DatetimeUtils::isDatetimeNotEmptyOrNull($callResult->call_date) ? DatetimeUtils::formatDate($callResult->call_date, $formatDate) : "";
                if (DatetimeUtils::isDatetimeNotEmptyOrNull($callResult->appointment_date)) {
                    $callResult->hour = DatetimeUtils::formatDate($callResult->appointment_date, 'H');
                    $callResult->minute = DatetimeUtils::formatDate($callResult->appointment_date, 'i');
                    $callResult->appointment_date = DatetimeUtils::formatDate($callResult->appointment_date, $formatDate);
                } else {
                    $callResult->appointment_date = "";
                }
            } else {
                $callResult->call_date = DatetimeUtils::getCurrentDateDependOnDevice();
                $callResult->appointment_date = "";
            }
        }
        $backUrl = UrlUtils::buildGoBackUrl();
        return $this->render('update', [
            'model' => $callResult,
            'chanels' => ChanelUtil::getDropdownList(false),
            'purposes' => PurposeUtil::getDropdownList(false),
            'customers' => CustomerUtil::getDropdownList(false),
            'backUrl' => !empty($backUrl) ? $backUrl : '/call-result/'
        ]);
    }

    private function updateData($model, $id)
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $model->call_date = DatetimeUtils::convertStringToDate($model->call_date);
            if (!empty($model->appointment_date)) {
                $model->appointment_date = DatetimeUtils::convertStringToDateTime($model->appointment_date, $model->hour, $model->minute);
            } else {
                $model->appointment_date = null;
            }

            $lastId = $id;
            if (UrlUtils::isEditAction($id)) {
                $params = [
                    'customer_id',
                    'chanel_id',
                    'call_date',
                    'is_new_call',
                    'purpose_id',
                    'result',
                    'updated_by',
                    'updated_at'
                ];
                if (!empty($model->appointment_date)) {
                    $params[] = 'appointment_date';
                }
                $model->updated_by = SessionUtils::getUsername();
                $model->updated_at = date('Y-m-d H:i:s');
                $model->save(true, $params);
            } else {
                $model->save();
                $lastId = Yii::$app->db->getLastInsertID();
                if(!empty($model->schedule_id)){
                    $personalSchedule = PersonalSchedule::findOne(['id'=>$model->schedule_id]);
                    if(!is_null($personalSchedule)){
                        $param_personal_schedules = [
                            'completed',
                            'completed_date',
                            'updated_by',
                            'updated_at'
                        ];
                        $personalSchedule->completed = 1;
                        $personalSchedule->completed_date = DatetimeUtils::getCurrentDate();
                        $personalSchedule->updated_by = SessionUtils::getUsername();
                        $personalSchedule->updated_at = date('Y-m-d H:i:s');
                        $personalSchedule->save(true, $param_personal_schedules);
                    }
                }
            }
            if ($model->is_add_schedule == 1 && !empty($model->appointment_date)) {
                $personalSchedule = new PersonalSchedule();
                if (UrlUtils::isEditAction($id)) {
                    $personalSchedule = PersonalSchedule::findOne(['created_from' => CallResult::CALL_RESULT . '-' . $lastId]);
                    if (is_null($personalSchedule)) {
                        $personalSchedule = new PersonalSchedule();
                    }
                }
                $personalSchedule->customer_id = $model->customer_id;
                $personalSchedule->is_new_customer = 0;
                $personalSchedule->chanel_id = $model->chanel_id;
                $personalSchedule->purpose_id = $model->purpose_id;
                $personalSchedule->date = $model->appointment_date;
                $personalSchedule->is_call = 1;
                $personalSchedule->completed = 0;
                $personalSchedule->created_from = CallResult::CALL_RESULT . '-' . $lastId;
                $personalSchedule->save();
            } else {
                $where = array('created_from' => CallResult::CALL_RESULT . '-' . $lastId);
                PersonalSchedule::deleteAll($where);
            }
            $transaction->commit();
            return true;
        } catch (Exception $e) {
            $transaction->rollBack();
        }
        return false;
    }

    public function actionDelete()
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $post = Yii::$app->request->post();
            $values = null;
            if (isset($post)) {
                $values = isset($post["values"]) ? $post["values"] : null;
            }
            if (!is_null($values)) {
                foreach ($values as $index => $value) {
                    $where = array('created_from' => CallResult::CALL_RESULT . '-' . $value);
                    PersonalSchedule::deleteAll($where);
                }
            }
            DeleteDataUtil::delete(new CallResult());
            $transaction->commit();
            return true;
        } catch (Exception $e) {
            $transaction->rollBack();
        }
        return false;
    }
}