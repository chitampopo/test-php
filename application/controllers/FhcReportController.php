<?php
/**
 * Created by PhpStorm.
 * User: Tam
 * Date: 9/14/2018
 * Time: 7:42 PM
 */

namespace application\controllers;

use application\models\Customer\Customer;
use application\models\FhcReport\DemandUtils;
use application\models\FhcReport\FhcReportSearch;
use application\models\Job\Job;
use application\models\MaritalStatus\MaritalStatus;
use application\models\User\User;
use application\models\User\UserUtil;
use application\utilities\DatetimeUtils;
use application\utilities\NumberUtils;
use application\utilities\PermissionUtil;
use application\utilities\SessionUtils;
use yii\web\Controller;
use yii;
use yii\helpers\Url;
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Style_Alignment;

class FhcReportController extends Controller
{

    public function beforeAction($action)
    {
        PermissionUtil::canAccess('fhc-report');
        return parent::beforeAction($action);
    }

    public function actionIndex()
    {
        $fhcReportSearch = new FhcReportSearch();
        $params = Yii::$app->request->get();
        $fhcReportSearch->from_date =  DatetimeUtils::getFirstDayOfMonthDateDependOnDevice();
        $fhcReportSearch->to_date = DatetimeUtils::getCurrentDateDependOnDevice();
        $data = $fhcReportSearch->search($params);
        $users = UserUtil::getDropdownList(false);

        if(!PermissionUtil::isXPMRole() && !PermissionUtil::isXPRole()) {
            if (isset($params["FhcReportSearch"])) {
                $getFhcReportSearch = $params["FhcReportSearch"];
                $department_id = isset($getFhcReportSearch["department_id"]) ? $getFhcReportSearch["department_id"] : "";
                if(!empty($department_id)) {
                    $users = UserUtil::getDropdownListRelatedToUsers(false, UserUtil::getUserByDepartment($department_id));
                }
            }
        }
        return $this->render('index', [
            'data' => $data,
            'fhcReportSearch' => $fhcReportSearch,
            'users' => $users
        ]);
    }

    public function actionExportExcel()
    {
        $temp_folder = "temp";
        $fileUrl = "";
        $post = Yii::$app->request->post();
        if (isset($post)) {
            $model = new FhcReportSearch();
            $from_date = isset($post['from_date']) ? $post['from_date'] : DatetimeUtils::getFirstDayOfMonthDateDependOnDevice();
            $to_date = isset($post['to_date']) ? $post['to_date'] : DatetimeUtils::getCurrentDateDependOnDevice();
            $customer_id = isset($post['customer_id']) ? $post['customer_id'] : "";
            $department_id = isset($post['department_id']) ? $post['department_id'] : "";
            $user_id = isset($post['user_id']) ? $post['user_id'] : "";

            $params = array(
                "FhcReportSearch" => array(
                    "from_date" => $from_date,
                    "to_date" => $to_date,
                    "customer_id" => $customer_id,
                    "department_id" => $department_id,
                    "user_id" => $user_id
                )
            );

            $data = $model->search($params, true);
            $objPHPExcel = new PHPExcel();

            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel2007");
            $objSheet = $objPHPExcel->getActiveSheet();
            $objPHPExcel->getDefaultStyle()->getFont()->setName('Times New Roman');
            $objPHPExcel->getDefaultStyle()->getFont()->setSize(12);

            // set tieu de bao cao
            $objSheet->setCellValue('A1', "FHC REPORT");
            if (!PermissionUtil::isXPRole()) {
                $objSheet->mergeCells('A1:N1');
            } else {
                $objSheet->mergeCells('A1:M1');
            }

            // set tên các cột
            $objSheet->setCellValue('A2', "STT");
            $objSheet->setCellValue('B2', "Tên khách hàng");
            $objSheet->setCellValue('C2', "SĐT");
            $objSheet->setCellValue('D2', "Địa chỉ");
            $objSheet->setCellValue('E2', "Tuổi");
            $objSheet->setCellValue('F2', "Tình trạng hôn nhân");
            $objSheet->setCellValue('G2', "Số con");
            $objSheet->setCellValue('H2', "Nhu cầu");
            $objSheet->setCellValue('I2', "Nghề nghiệp");
            $objSheet->setCellValue('J2', "Mức lương");
            $objSheet->setCellValue('K2', "SIS");
            $objSheet->setCellValue('L2', "KHTN");
            $objSheet->setCellValue('M2', "JFW");
            if (!PermissionUtil::isXPRole()) {
                $objSheet->setCellValue('N2', "Nhân viên");
            }

            // Format tiêu đề
            if (!PermissionUtil::isXPRole()) {
                $rangeTitle = $objSheet->getStyle("A1:N1");
            } else {
                $rangeTitle = $objSheet->getStyle("A1:M1");
            }
            $rangeTitle->getFont()->setBold(true);
            $rangeTitle->getFont()->setSize(18);
            $rangeTitle->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            // Format tiêu đề
            if (!PermissionUtil::isXPRole()) {
                $rangeTitleTable = $objSheet->getStyle("A2:N2");
            } else {
                $rangeTitleTable = $objSheet->getStyle("A2:M2");
            }
            $rangeTitleTable->getFont()->setBold(true);
            $rangeTitleTable->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            // Set kích thước các cột
            $objSheet->getColumnDimension("A")->setWidth(5);
            $objSheet->getColumnDimension("B")->setWidth(25);
            $objSheet->getColumnDimension("C")->setWidth(15);
            $objSheet->getColumnDimension("D")->setWidth(15);
            $objSheet->getColumnDimension("E")->setWidth(5);
            $objSheet->getColumnDimension("F")->setWidth(15);
            $objSheet->getColumnDimension("G")->setWidth(7);
            $objSheet->getColumnDimension("H")->setWidth(20);
            $objSheet->getColumnDimension("I")->setWidth(11);
            $objSheet->getColumnDimension("J")->setWidth(11);
            $objSheet->getColumnDimension("K")->setWidth(5);
            $objSheet->getColumnDimension("L")->setWidth(7);
            $objSheet->getColumnDimension("M")->setWidth(5);
            if (!PermissionUtil::isXPRole()) {
                $objSheet->getColumnDimension("N")->setWidth(25);
            }
            // binding data
            if ($data->getTotalCount() > 0) {
                $row = 3;
                $stt = 1;
                foreach ($data->getModels() as $index => $model) {
                    $customer = Customer::findOne(['id' => $model->customer_id]);
                    $marialStatus = MaritalStatus::findOne(['id' => $model->marital_status_id]);

                    $objSheet->setCellValue('A' . $row, $stt++);
                    $objSheet->setCellValue('B' . $row, !is_null($customer) ? $customer->name : "");
                    $objSheet->setCellValue('C' . $row, !is_null($customer) ? $customer->phone : "");
                    $objSheet->setCellValue('D' . $row, !is_null($customer) ? $customer->address : "");
                    if(!is_null($customer)) {
                        if (DatetimeUtils::isDatetimeNotEmptyOrNull($customer->birthday)) {
                            $age = date('Y') - DatetimeUtils::formatDate($customer->birthday, 'Y');
                            $objSheet->setCellValue('E' . $row, $age);
                        } else {
                            $objSheet->setCellValue('E' . $row, "");
                        }
                    }else{
                        $objSheet->setCellValue('E' . $row, "");
                    }

                    $objSheet->setCellValue('F' . $row, !is_null($marialStatus) ? $marialStatus->name : "");
                    $objSheet->setCellValue('G' . $row, $model->number_of_children);

                    $nhuCau = "";
                    $array = explode(',', $model->demand);
                    foreach ($array as $index => $item) {
                        $ten = DemandUtils::getName($item);
                        if (!empty($ten)) {
                            $nhuCau .= $ten . ", ";
                        }
                    }

                    $objSheet->setCellValue('H' . $row, $nhuCau);
                    $job = Job::findOne(['id'=>$model->job_id]);
                    if(!is_null($job)){
                        $objSheet->setCellValue('I' . $row, $job->name);
                    }else{
                        $objSheet->setCellValue('I' . $row, "");
                    }

                    $objSheet->setCellValue('J' . $row, $model->salary);
                    $objSheet->setCellValue('K' . $row, $model->sis == 1 ? "Yes" : "No");
                    $objSheet->setCellValue('L' . $row, $model->khtn);
                    $objSheet->setCellValue('M' . $row, $model->jfw == 1 ? "Yes" : "No");
                    if (!PermissionUtil::isXPRole()) {
                        $user = User::findOne(['id' => $model->user_id]);
                        $objSheet->setCellValue('N' . $row, !is_null($user) ? $user->name : "");
                    }
                    $row++;
                }
                $col = "M";
                if (!PermissionUtil::isXPRole()) {
                    $col = "N";
                }
                // bao khung các dòng dữ liệu
                $objSheet->getStyle("A2:{$col}" . ($row - 1))->applyFromArray(
                    array(
                        'borders' => array(
                            'allborders' => array(
                                'style' => \PHPExcel_Style_Border::BORDER_THIN,
                                'color' => array('rgb' => '000000')
                            )
                        )
                    )
                );
                $objSheet->getStyle("A2:{$col}" . $row)->getAlignment()->setWrapText(true);
                $objSheet->getStyle("A2:{$col}" . ($row - 1))->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_TOP);
            }

            $objSheet->setTitle('FHC REPORT');

            $objPHPExcel->setActiveSheetIndex(0);
            $fileName = "FHCReport_" . SessionUtils::getUsername() . ".xlsx";
            $fileUrl = yii\helpers\Url::to([$temp_folder . "/" . $fileName]);
            $objWriter->save($temp_folder . DIRECTORY_SEPARATOR . $fileName);
        }
        return $fileUrl;
    }
}