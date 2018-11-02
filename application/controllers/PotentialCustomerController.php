<?php
/**
 * Created by PhpStorm.
 * User: phuocnguyen
 * Date: 09/09/2018
 * Time: 4:02 PM
 */

namespace application\controllers;


use application\models\Chanel\Chanel;
use application\models\Chanel\ChanelUtil;
use application\models\Customer\Customer;
use application\models\Customer\CustomerUtil;
use application\models\PotentialCustomer\PotentialCustomer;
use application\models\PotentialCustomer\PotentialCustomerSearch;
use application\models\User\User;
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
use yii\helpers\Url;
use yii\web\Controller;
use Yii;
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Style_Alignment;

class PotentialCustomerController extends Controller
{
    public function beforeAction($action)
    {
        PermissionUtil::canAccess('potential-customer');
        return parent::beforeAction($action);
    }

    public function actionIndex()
    {
        $potentialCustomerSearch = new PotentialCustomerSearch();
        $params = Yii::$app->request->get();
        $potentialCustomerSearch->date = DatetimeUtils::getCurrentDateDependOnDevice();
        $data = $potentialCustomerSearch->search($params);
        $users = UserUtil::getDropdownList(false);
        if (!PermissionUtil::isXPMRole() && !PermissionUtil::isXPRole()) {
            if (isset($params["PotentialCustomerSearch"])) {
                $getFhcReportSearch = $params["PotentialCustomerSearch"];
                $department_id = isset($getFhcReportSearch["department_id"]) ? $getFhcReportSearch["department_id"] : "";
                if (!empty($department_id)) {
                    $users = UserUtil::getDropdownListRelatedToUsers(false, UserUtil::getUserByDepartment($department_id));
                }
            }
        }
        return $this->render('index', [
            'data' => $data,
            'potentialCustomerSearch' => $potentialCustomerSearch,
            'chanels' => ChanelUtil::getDropdownList(false),
            'users' => $users
        ]);
    }

    public function actionUpdate($id = null)
    {
        $potentialCustomer = new PotentialCustomer();
        $customer = new Customer();
        $isHaveAnError = false;
        if (UrlUtils::isEditAction($id)) {
            $potentialCustomer = PotentialCustomer::findOne(['id' => $id]);
            $customer = Customer::findOne(['id' => $potentialCustomer->customer_id]);
            $personalSchedule = PersonalSchedule::findOne(['created_from' => PotentialCustomer::POTENTIAL_RESULT . '-' . $id]);
            if (!is_null($personalSchedule)) {
                $potentialCustomer->is_add_schedule = 1;
            }
        }
        $post = Yii::$app->request->post();
        if ($potentialCustomer->load($post) && $customer->load($post) && $customer->validate()) {
            $postPotentialCustomer = $post['PotentialCustomer'];
            $potentialCustomer->is_add_schedule = isset($postPotentialCustomer['is_add_schedule']) ? $postPotentialCustomer['is_add_schedule'] : 0;
            $potentialCustomer->hour = isset($postPotentialCustomer['hour']) ? $postPotentialCustomer['hour'] : 0;
            $potentialCustomer->minute = isset($postPotentialCustomer['minute']) ? $postPotentialCustomer['minute'] : 0;

            $scheduled_meeting_date = "";
            if (!empty($potentialCustomer->scheduled_meeting_date)) {
                $scheduled_meeting_date = DatetimeUtils::convertStringToDateTime($potentialCustomer->scheduled_meeting_date, $potentialCustomer->hour, $potentialCustomer->minute);
            }
            $personalScheduleByDatetime = PersonalScheduleUrlUtils::getPersonalScheduleByDateTime($scheduled_meeting_date, "");
            if (count($personalScheduleByDatetime) > 0 && $potentialCustomer->is_add_schedule == 1) {
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
                $result = $this->updateData($potentialCustomer, $customer, $id);
                MessageUtils::showMessage($result);
                if (!UrlUtils::isEditAction($id)) {
                    $potentialCustomer = new PotentialCustomer();
                    $customer = new Customer();
                }
            }
        }
        if(!$isHaveAnError) {
            if (UrlUtils::isEditAction($id)) {
                $formatDate = "d/m/Y";
                if (DetectDeviceUtil::isMobile()) {
                    $formatDate = "Y-m-d";
                }
                $potentialCustomer->date = DatetimeUtils::isDatetimeNotEmptyOrNull($potentialCustomer->date) ? DatetimeUtils::formatDate($potentialCustomer->date, $formatDate) : "";
                if (DatetimeUtils::isDatetimeNotEmptyOrNull($potentialCustomer->scheduled_meeting_date)) {
                    $potentialCustomer->hour = DatetimeUtils::formatDate($potentialCustomer->scheduled_meeting_date, 'H');
                    $potentialCustomer->minute = DatetimeUtils::formatDate($potentialCustomer->scheduled_meeting_date, 'i');
                    $potentialCustomer->scheduled_meeting_date = DatetimeUtils::formatDate($potentialCustomer->scheduled_meeting_date, $formatDate);
                } else {
                    $potentialCustomer->scheduled_meeting_date = "";
                }
            } else {
                $potentialCustomer->date = DatetimeUtils::getCurrentDateDependOnDevice();
                $potentialCustomer->scheduled_meeting_date = "";
            }
        }
        return $this->render('update', [
            'model' => $potentialCustomer,
            'customer' => $customer,
            'chanels' => ChanelUtil::getDropdownList(false),
            'customers' => CustomerUtil::getDropdownList(false)
        ]);
    }

    private function updateData($model, $customer, $id)
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $model->date = DatetimeUtils::convertStringToDate($model->date);
            if (!empty($model->scheduled_meeting_date)) {
                $model->scheduled_meeting_date = DatetimeUtils::convertStringToDateTime($model->scheduled_meeting_date, $model->hour, $model->minute);
            } else {
                $model->scheduled_meeting_date = null;
            }
            $customer->is_lock_change_category = 0;
            $customer->disabled = 0;
            $customer->chanel_id = $model->chanel_id;
            $customer->updated_by = SessionUtils::getUsername();
            $customer->updated_at = date('Y-m-d H:i:s');
            $lastId = $id;
            if (UrlUtils::isEditAction($id)) {
                $params = [
                    'customer_id',
                    'customer_referral_id',
                    'chanel_id',
                    'date',
                    'scheduled_meeting_date',
                    'updated_by',
                    'updated_at'
                ];

                $model->updated_by = SessionUtils::getUsername();
                $model->updated_at = date('Y-m-d H:i:s');
                $model->save(true, $params);

                $param_customer = [
                    'name',
                    'phone',
                    'chanel_id',
                    'updated_by',
                    'updated_at'
                ];
                $customer->save(true, $param_customer);
            } else {
                $customer->save();
                $lastCustomerId = Yii::$app->db->getLastInsertID();
                $model->customer_id = $lastCustomerId;
                $model->save();
                $lastId = Yii::$app->db->getLastInsertID();
            }

            if ($model->is_add_schedule == 1 && !empty($model->scheduled_meeting_date)) {
                $personalSchedule = new PersonalSchedule();
                if (UrlUtils::isEditAction($id)) {
                    $personalSchedule = PersonalSchedule::findOne(['created_from' => PotentialCustomer::POTENTIAL_RESULT . '-' . $lastId]);
                    if (is_null($personalSchedule)) {
                        $personalSchedule = new PersonalSchedule();
                    }
                }
                $personalSchedule->customer_id = $model->customer_id;
                $personalSchedule->is_new_customer = 0;
                $personalSchedule->chanel_id = $model->chanel_id;
                $personalSchedule->date = $model->scheduled_meeting_date;
                $personalSchedule->is_call = 1;
                $personalSchedule->completed = 0;
                $personalSchedule->created_from = PotentialCustomer::POTENTIAL_RESULT . '-' . $lastId;
                $personalSchedule->save();
            } else {
                $where = array('created_from' => PotentialCustomer::POTENTIAL_RESULT . '-' . $lastId);
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
        return DeleteDataUtil::delete(new PotentialCustomer());
    }

    public function actionExportExcel()
    {
        $temp_folder = "temp";
        $fileUrl = "";
        $post = Yii::$app->request->post();
        if (isset($post)) {
            $model = new PotentialCustomerSearch();
            $khachHang = isset($post['khachHang']) ? $post['khachHang'] : "";
            $ngay = isset($post['ngay']) ? $post['ngay'] : "";
            $nhanVien = isset($post['nhanVien']) ? $post['nhanVien'] : "";

            $params = array(
                "PotentialCustomerSearch" => array(
                    "customer_id" => $khachHang,
                    "user_id" => $nhanVien,
                    "date" => $ngay
                )
            );

            $data = $model->search($params, true);
            $objPHPExcel = new PHPExcel();

            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel2007");
            $objSheet = $objPHPExcel->getActiveSheet();
            $objPHPExcel->getDefaultStyle()->getFont()->setName('Times New Roman');
            $objPHPExcel->getDefaultStyle()->getFont()->setSize(12);

            // set tieu de bao cao
            $objSheet->setCellValue('A1', "KẾT QUẢ XIN KHTN");
            $objSheet->mergeCells('A1:F1');

            // set tên các cột
            $objSheet->setCellValue('A2', "STT");
            $objSheet->setCellValue('B2', "Tên khách hàng");
            $objSheet->setCellValue('C2', "SĐT");
            $objSheet->setCellValue('D2', "Người giới thiệu");
            $objSheet->setCellValue('E2', "Nguồn");
            $objSheet->setCellValue('F2', "Dự kiến gặp");
            $objSheet->setCellValue('G2', "Nhân viên");

            // Format tiêu đề
            $rangeTitle = $objSheet->getStyle("A1:H1");
            $rangeTitle->getFont()->setBold(true);
            $rangeTitle->getFont()->setSize(18);
            $rangeTitle->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            // Format tiêu đề
            $rangeTitleTable = $objSheet->getStyle("A2:H2");
            $rangeTitleTable->getFont()->setBold(true);
            $rangeTitleTable->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            // Set kích thước các cột
            $objSheet->getColumnDimension("A")->setWidth(5);
            $objSheet->getColumnDimension("B")->setWidth(25);
            $objSheet->getColumnDimension("C")->setWidth(15);
            $objSheet->getColumnDimension("D")->setWidth(25);
            $objSheet->getColumnDimension("E")->setWidth(20);
            $objSheet->getColumnDimension("F")->setWidth(17);
            $objSheet->getColumnDimension("G")->setWidth(25);
            // binding data
            if ($data->getTotalCount() > 0) {
                $row = 3;
                $stt = 1;
                foreach ($data->getModels() as $index => $model) {
                    $customer = Customer::findOne(['id' => $model->customer_id]);
                    $customer_refer = Customer::findOne(['id' => $model->customer_referral_id]);
                    $chanel = Chanel::findOne(['id' => $model->chanel_id]);
                    $user = User::findOne(['id' => $model->user_id]);

                    $objSheet->setCellValue('A' . $row, $stt++);
                    $objSheet->setCellValue('B' . $row, !is_null($customer) ? $customer->name : "");
                    $objSheet->setCellValue('C' . $row, !is_null($customer) ? $customer->phone: "");
                    $objSheet->setCellValue('D' . $row, !is_null($customer_refer) ? $customer_refer->name : "");
                    $objSheet->setCellValue('E' . $row, $chanel->name);
                    $objSheet->setCellValue('F' . $row, DatetimeUtils::isDatetimeNotEmptyOrNull($model->scheduled_meeting_date) ? DatetimeUtils::formatDate($model->scheduled_meeting_date, "H\hi d/m/Y") : "");
                    $objSheet->setCellValue('G' . $row, !is_null($user) ? $user->name : "");
                    $row++;
                }
                // bao khung các dòng dữ liệu
                $objSheet->getStyle("A2:G" . $row)->applyFromArray(
                    array(
                        'borders' => array(
                            'allborders' => array(
                                'style' => \PHPExcel_Style_Border::BORDER_THIN,
                                'color' => array('rgb' => '000000')
                            )
                        )
                    )
                );
                $objSheet->getStyle("A4:H" . $row)->getAlignment()->setWrapText(true);
                $objSheet->getStyle("A4:H" . $row)->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_TOP);
            }

            $objSheet->setTitle('Kết quản KHTN');

            $objPHPExcel->setActiveSheetIndex(0);
            $fileName = "KetQuaKHTN_" . SessionUtils::getUsername() . ".xlsx";
            $fileUrl = Url::to([$temp_folder . "/" . $fileName]);
            $objWriter->save($temp_folder . DIRECTORY_SEPARATOR . $fileName);
        }
        return $fileUrl;
    }
}