<?php
/**
 * Created by PhpStorm.
 * User: Tam
 * Date: 9/9/2018
 * Time: 2:23 PM
 */

namespace application\controllers;

use application\models\Chanel\ChanelUtil;
use application\models\Customer\Customer;
use application\models\Customer\CustomerUtil;
use application\models\FhcReport\DemandUtils;
use application\models\FhcReport\FhcReport;
use application\models\Job\JobUtil;
use application\models\MeetingResult\MeetingResult;
use application\models\MeetingResult\MeetingResultSearch;
use application\models\Purpose\PurposeUtil;
use application\models\User\UserUtil;
use application\models\PersonalSchedule\PersonalSchedule;
use application\utilities\DatetimeUtils;
use application\utilities\DeleteDataUtil;
use application\utilities\DetectDeviceUtil;
use application\utilities\MessageUtils;
use application\utilities\PermissionUtil;
use application\utilities\PersonalScheduleUrlUtils;
use application\utilities\SessionUtils;
use application\utilities\UrlUtils;
use yii\web\Controller;
use Yii;

class MeetingResultController extends Controller
{

    public function beforeAction($action)
    {
        PermissionUtil::canAccess('meeting-result');
        return parent::beforeAction($action);
    }

    public function actionIndex()
    {
        $meetingResultSearch = new MeetingResultSearch();
        $meetingResultSearch->meeting_date = DatetimeUtils::getCurrentDateDependOnDevice();
        $meetingResultSearch->from_date = DatetimeUtils::getCurrentDateDependOnDevice();
        $params = Yii::$app->request->get();
        $data = $meetingResultSearch->search($params);
        $users = UserUtil::getDropdownList(false);

        if (!PermissionUtil::isXPMRole() && !PermissionUtil::isXPRole()) {
            if (isset($params["MeetingResultSearch"])) {
                $getFhcReportSearch = $params["MeetingResultSearch"];
                $department_id = isset($getFhcReportSearch["department_id"]) ? $getFhcReportSearch["department_id"] : "";
                if (!empty($department_id)) {
                    $users = UserUtil::getDropdownListRelatedToUsers(false, UserUtil::getUserByDepartment($department_id));
                }
            }
        }
        return $this->render('index', [
            'data' => $data,
            'meetingResultSearch' => $meetingResultSearch,
            'chanels' => ChanelUtil::getDropdownList(false),
            'users' => $users

        ]);
    }

    public function actionUpdate($id = null)
    {
        $meetingResult = new MeetingResult();
        $meetingResult->fhc_report = new FhcReport();
        $personalSchedule = new PersonalSchedule();
        $isHaveAnError = false;
        if (UrlUtils::isEditAction($id)) {
            Yii::info('Load meeting result');
            $meetingResult = MeetingResult::findOne(['id' => $id]);
            $meetingResult->fhc_report = FhcReport::findOne(['meeting_result_id' => $id]);
            $personalSchedule = PersonalSchedule::findOne(['created_from' => MeetingResult::MEETING_RESULT . '-' . $id]);
            if (!is_null($personalSchedule)) {
                $meetingResult->is_add_schedule = 1;
            }
        } else {
            Yii::info('Create meeting result');
            $get = Yii::$app->request->get();
            if(isset($get["customer"])){
                $meetingResult->customer_id = $get["customer"];
            }
            if (isset($get['result-id'])) {
                $meetingResult->customer_id = !empty($get['result-id']) ? $get['result-id'] : null;
            } elseif (isset($get['schedule-id']) && !empty($get['schedule-id'])) {
                $personalSchedule = PersonalSchedule::findOne(['id' => $get['schedule-id']]);
                if (!is_null($personalSchedule)) {
                    $meetingResult->customer_id = $personalSchedule->customer_id;
                    $meetingResult->chanel_id = $personalSchedule->chanel_id;
                    $meetingResult->schedule_id = $personalSchedule->id;
                }
            }
        }

        $post = Yii::$app->request->post();
        if ($meetingResult->load($post)) {
            $meetingResult->customer_id = $post['MeetingResult']['customer_id'];
            $postMeetingResult = $post['MeetingResult'];
            $meetingResult->fhc_report = $post['FhcReport'];
            $meetingResult->is_add_schedule = isset($postMeetingResult['is_add_schedule']) ? $postMeetingResult['is_add_schedule'] : 0;
            $meetingResult->user_id = SessionUtils::getUserId();
            $meetingResult->hour = isset($postMeetingResult['hour']) ? $postMeetingResult['hour'] : 0;
            $meetingResult->minute = isset($postMeetingResult['minute']) ? $postMeetingResult['minute'] : 0;
            $meetingResult->schedule_id = isset($postMeetingResult['schedule_id']) ? $postMeetingResult['schedule_id'] : 0;
            $meeting_date = "";
            if (!empty($meetingResult->meeting_date)) {
                $meeting_date = DatetimeUtils::convertStringToDateTime($meetingResult->meeting_date, $meetingResult->hour, $meetingResult->minute);
            }
            $personalScheduleByDatetime = PersonalScheduleUrlUtils::getPersonalScheduleByDateTime($meeting_date, !is_null($personalSchedule) ? $personalSchedule->id : "");
            if (count($personalScheduleByDatetime) > 0 && $meetingResult->is_add_schedule == 1) {
                $listDuplicateSchedule = "";
                foreach ($personalScheduleByDatetime as $index => $item) {
                    $customer = Customer::findOne(['id' => $item->customer_id]);
                    $customer_name = "";
                    if (!is_null($customer)) {
                        $customer_name = $customer->name;
                    }
                    $listDuplicateSchedule .= $customer_name . " (" . DatetimeUtils::formatDate($item->date, "d/m/Y H:i") . ") ";
                }
                $isHaveAnError = true;
                MessageUtils::showMessage(false, "Bạn đã có lịch hẹn {$listDuplicateSchedule}, vui lòng chọn thời gian khác");
            } else {
                $result = $this->updateData($meetingResult, $id);
                MessageUtils::showMessage($result);
                if (!UrlUtils::isEditAction($id)) {
                    $meetingResult = new MeetingResult();
                    $meetingResult->fhc_report = new FhcReport();
                }
            }
        }
        if (!$isHaveAnError) {
            if (UrlUtils::isEditAction($id)) {
                $formatDate = "d/m/Y";
                if (DetectDeviceUtil::isMobile()) {
                    $formatDate = "Y-m-d";
                }
                $meetingResult->meeting_date = DatetimeUtils::formatDate($meetingResult->meeting_date, $formatDate);
                if (DatetimeUtils::isDatetimeNotEmptyOrNull($meetingResult->follow_up_date)) {
                    $meetingResult->hour = DatetimeUtils::formatDate($meetingResult->follow_up_date, 'H');
                    $meetingResult->minute = DatetimeUtils::formatDate($meetingResult->follow_up_date, 'i');
                    $meetingResult->follow_up_date = DatetimeUtils::formatDate($meetingResult->follow_up_date, $formatDate);
                } else {
                    $meetingResult->follow_up_date = "";
                }
            } else {
                $meetingResult->meeting_date = DatetimeUtils::getCurrentDateDependOnDevice();
            }
        }

        if (UrlUtils::isEditAction($id)) {
            $meetingResult->fhc_report = FhcReport::findOne(['meeting_result_id' => $id]);
            if ($meetingResult->fhc_report != null) {
                Yii::info('$meetingResult->fhc_report->demand: ' . $meetingResult->fhc_report->demand);
                if (!empty($meetingResult->fhc_report->demand)) {
                    Yii::info('$meetingResult->fhc_report->demand: ' . $meetingResult->fhc_report->demand);
                    $meetingResult->fhc_report->demand = explode(",", $meetingResult->fhc_report->demand);
                }
            } else {
                Yii::info('Have no fhc report before');
                $meetingResult->fhc_report = new FhcReport();
            }
        } else {
            if (isset($meetingResult->fhc_report->demand)) {
                $meetingResult->fhc_report->demand = explode(",", $meetingResult->fhc_report->demand);
            }
        }
        $backUrl = UrlUtils::buildGoBackUrl();
        return $this->render('update', [
            'model' => $meetingResult,
            'chanels' => ChanelUtil::getDropdownList(false),
            'customers' => CustomerUtil::getDropdownList(false),
            'demands' => DemandUtils::getDropDownList(),
            'jobs' => JobUtil::getDropdownList(false),
            'backUrl' => !empty($backUrl) ? $backUrl : '/meeting-result/'
        ]);
    }

    private function updateData($model, $id)
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $model->meeting_date = DatetimeUtils::convertStringToDate($model->meeting_date);
            if (!empty($model->follow_up_date)) {
                $model->follow_up_date = DatetimeUtils::convertStringToDateTime($model->follow_up_date, $model->hour, $model->minute);
            } else {
                $model->follow_up_date = null;
            }

            $lastId = $id;
            if (UrlUtils::isEditAction($id)) {
                $params = [
                    'customer_id',
                    'chanel_id',
                    'user_id',
                    'meeting_date',
                    'is_new_meeting',
                    'hd',
                    'fhc',
                    'sis',
                    'warm',
                    'khtn',
                    'other',
                    'follow_up_date',
                    'result'
                ];

                $model->updated_by = SessionUtils::getUsername();
                $model->updated_at = date('Y-m-d H:i:s');
                $model->save(true, $params);
                if ($model->fhc != 0) {
                    if (empty($model->fhc_report['id'])) {
                        $fhc_report = new FhcReport();
                        $fhc_report->customer_id = $model->customer_id;
                        $fhc_report->user_id = SessionUtils::getUserId();
                        $fhc_report->date = $model->meeting_date;
                        $fhc_report->job = $model['fhc_report']['job_id'];
                        $fhc_report->salary = $model['fhc_report']['salary'];
                        $fhc_report->marital_status_id = CustomerUtil::getCustomerByID($fhc_report->customer_id)->marital_status_id;
                        $fhc_report->number_of_children = CustomerUtil::getCustomerByID($fhc_report->customer_id)->number_of_children;
                        if (!empty($model['fhc_report']['demand'])) {
                            $fhc_report->demand = implode(",", $model['fhc_report']['demand']);
                        }
                        $fhc_report->salary = (int)str_replace(',', '', $model['fhc_report']['salary']);
                        $fhc_report->sis = $model->sis;
                        $fhc_report->khtn = $model['fhc_report']['khtn'];;
                        $fhc_report->created_by = SessionUtils::getUsername();
                        $fhc_report->created_at = date('Y-m-d H:i:s');
                        $fhc_report->jfw = "1";
                        $fhc_report->meeting_result_id = $model->id;

                        $fhc_report->save(false);
                    } else {
                        $fhc_report = FhcReport::findOne(['id' => $model->fhc_report['id']]);
                        if (count($model['fhc_report']['demand']) > 0) {
                            $fhc_report->demand = !empty($model['fhc_report']['demand']) ? implode(",", $model['fhc_report']['demand']) : null;
                        }
                        $fhc_report->job_id = !empty($model['fhc_report']['job'])?$model['fhc_report']['job']:null;
                        $fhc_report->salary = !empty($model['fhc_report']['salary']) ? (int)str_replace(',', '', $model['fhc_report']['salary']) : null;
                        $fhc_report->khtn = $model['fhc_report']['khtn'];
                        $fhc_report->updated_by = SessionUtils::getUsername();
                        $fhc_report->updated_at = date('Y-m-d H:i:s');
                        $fhc_report->save(false);
                    }
                } else {
                    $where = array("id" => $model->fhc_report['id']);
                    FhcReport::deleteAll($where);
                }
            } else {
                $model->created_by = SessionUtils::getUsername();
                $model->created_at = date('Y-m-d H:i:s');
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

                if ($model->fhc != 0) {
                    $fhc_report = new FhcReport();
                    $fhc_report->customer_id = $model->customer_id;
                    $fhc_report->user_id = SessionUtils::getUserId();
                    $fhc_report->date = $model->meeting_date;
                    $fhc_report->job_id = $model['fhc_report']['job_id'];
                    $fhc_report->marital_status_id = CustomerUtil::getCustomerByID($fhc_report->customer_id)->marital_status_id;
                    $fhc_report->number_of_children = CustomerUtil::getCustomerByID($fhc_report->customer_id)->number_of_children;
                    $fhc_report->demand = !empty($model['fhc_report']['demand']) ? implode(",", $model['fhc_report']['demand']) : "";
                    $fhc_report->salary = !empty($model['fhc_report']['salary']) ? (int)str_replace(',', '', $model['fhc_report']['salary']) : null;
                    $fhc_report->sis = $model->sis;
                    $fhc_report->khtn = !empty($model['fhc_report']['khtn'])? $model['fhc_report']['khtn'] : null;
                    $fhc_report->created_by = SessionUtils::getUsername();
                    $fhc_report->created_at = date('Y-m-d H:i:s');
                    $fhc_report->jfw = "1";
                    $fhc_report->meeting_result_id = $lastId;
                    $fhc_report->save(false);
                }

            }

            if ($model->is_add_schedule == 1 && !empty($model->follow_up_date)) {
                $personalSchedule = new PersonalSchedule();
                if (UrlUtils::isEditAction($id)) {
                    $personalSchedule = PersonalSchedule::findOne(['created_from' => MeetingResult::MEETING_RESULT . '-' . $lastId]);
                    if (is_null($personalSchedule)) {
                        $personalSchedule = new PersonalSchedule();
                    }
                }
                $personalSchedule->customer_id = $model->customer_id;
                $personalSchedule->is_new_customer = 0;
                $personalSchedule->chanel_id = $model->chanel_id;
                $personalSchedule->date = $model->follow_up_date;
                $personalSchedule->is_call = 0;
                $personalSchedule->completed = 0;
                $personalSchedule->created_from = MeetingResult::MEETING_RESULT . '-' . $lastId;
                $personalSchedule->save();
            } else {
                $where = array('created_from' => MeetingResult::MEETING_RESULT . '-' . $lastId);
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
                    $where = array('created_from' => MeetingResult::MEETING_RESULT . '-' . $value);
                    PersonalSchedule::deleteAll($where);
                }
            }
            DeleteDataUtil::delete(new MeetingResult());
            $transaction->commit();
            return true;
        } catch (Exception $e) {
            $transaction->rollBack();
        }
        return false;
    }

}