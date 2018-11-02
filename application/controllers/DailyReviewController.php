<?php
/**
 * Created by PhpStorm.
 * User: phuocnguyen
 * Date: 13/09/2018
 * Time: 10:16 PM
 */

namespace application\controllers;
use application\models\CallResult\CallResult;
use application\models\Chanel\Chanel;
use application\models\Customer\Customer;
use application\models\DailyReview\DailyReview;
use application\models\Department\DepartmentUtil;
use application\models\MeetingResult\MeetingResult;
use application\models\Purpose\Purpose;
use application\models\User\User;
use application\models\User\UserUtil;
use application\utilities\DatetimeUtils;
use application\utilities\PermissionUtil;
use application\utilities\SessionUtils;
use yii\helpers\Url;
use yii\web\Controller;
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Style_Alignment;
use Yii;
class DailyReviewController extends Controller
{
    public function actionIndex(){

        $model = new DailyReview();
        $model->date = DatetimeUtils::getCurrentDateDependOnDevice();
        $users = User::find()
            ->andWhere(['is_active'=>1])
            ->orderBy(['name'=>SORT_ASC]);

        if(PermissionUtil::isXPMRole()){
            $id = SessionUtils::getDepartment()->id;
            $users->andWhere(['department_id'=>$id]);
            $model->department_id = $id;
        }
        $departments = DepartmentUtil::getDropdownList(PermissionUtil::isXPMRole());
        $userDropdowns = UserUtil::getDropdownListByUsers($users->all(), false);
        return $this->render('index', [
            'model' => $model,
            'users' => $userDropdowns,
            'departments' => $departments
        ]);
    }

    private function getCallResultData($date, $user){
        return CallResult::find()
            ->andWhere(["date_format(call_date,'%Y-%m-%d')" => $date])
            ->andWhere(['user_id'=>$user])
            ->all();
    }
    private function getMeetingResultData($date, $user){
        return MeetingResult::find()
            ->andWhere(["date_format(meeting_date,'%Y-%m-%d')" => $date])
            ->andWhere(['user_id'=>$user])
            ->all();
    }
    public function actionExportExcel()
    {
        $temp_folder = "temp";
        $fileUrl = "";
        $post = Yii::$app->request->post();
        if (isset($post)) {
            $date = isset($post['date']) ? $post['date'] : date('d/m/Y');
            $user = "";
            $department = "";
            if(PermissionUtil::isXPRole()){
                $user = SessionUtils::getUserId();
            }else{
                $department = isset($post['department']) ? $post['department'] : "";
                $user = isset($post['user']) ? $post['user'] : "";
            }
            $objPHPExcel = new PHPExcel();

            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel2007");
            $objSheet = $objPHPExcel->getActiveSheet();
            $objPHPExcel->getDefaultStyle()->getFont()->setName('Times New Roman');
            $objPHPExcel->getDefaultStyle()->getFont()->setSize(10);

            if(!empty($department)){
                if(!empty($user)){
                    $this->buildSheet($objSheet, User::findOne(['id'=>$user]), $date);
                }else {
                    $users = UserUtil::getUserByDepartment($department);
                    foreach ($users as $index => $item) {
                        $this->buildSheet($objSheet, $item, $date);
                        $objSheet = $objPHPExcel->createSheet();
                    }
                    $objPHPExcel->removeSheetByIndex(
                        $objPHPExcel->getIndex(
                            $objPHPExcel->getSheetByName('Worksheet')
                        )
                    );
                }
            }else{
                if(!empty($user)) {
                    $this->buildSheet($objSheet, User::findOne(['id' => $user]), $date);
                }
            }

            $objPHPExcel->setActiveSheetIndex(0);
            $fileName = "DailyReview_" . SessionUtils::getUsername() .".xlsx";
            $fileUrl = Url::to([$temp_folder . "/" . $fileName]);
            $objWriter->save($temp_folder . DIRECTORY_SEPARATOR . $fileName);
        }
        return $fileUrl;
    }

    private function buildSheet($objSheet, $user, $date){
        $nhanVien = $user;
        $nhanVienXpm = User::find()
            ->andWhere(['department_id' => $nhanVien->department_id])
            ->andWhere(['level_id' => 2])->one();

        $objSheet->setTitle($nhanVien->name);

        // set tieu de bao cao
        $objSheet->setCellValue('A1', "DAILY REVIEW");
        $objSheet->mergeCells('A1:N1');

        $rangeTitle = $objSheet->getStyle("A1:N1");
        $rangeTitle->getFont()->setBold(true);
        $rangeTitle->getFont()->setSize(18);
        $rangeTitle->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        $objSheet->setCellValue('A3', "XP");
        $objSheet->setCellValue('B3', !is_null($nhanVien)?$nhanVien->name:'');
        $objSheet->mergeCells('B3:D3');

        $objSheet->setCellValue('F3', "Ngày");
        $objSheet->setCellValue('G3', $date);
        $objSheet->mergeCells('G3:I3');

        $objSheet->setCellValue('A4', "XPM");
        $objSheet->setCellValue('B4', !is_null($nhanVienXpm) ? $nhanVienXpm->name : "");
        $objSheet->mergeCells('B4:D4');

        $objSheet->setCellValue('A6', "KẾT QUẢ CUỘC GỌI KHÁCH HÀNG");
        $objSheet->mergeCells('A6:J6');
        $rangeTitleCuocGoi = $objSheet->getStyle("A6:F6");
        $rangeTitleCuocGoi->getFont()->setBold(true);
        $rangeTitleCuocGoi->getFont()->setSize(14);

        // set tên các cột
        $objSheet->setCellValue('A8', "STT");
        $objSheet->setCellValue('B8', "Tên khách hàng");
        $objSheet->setCellValue('D8', "SĐT");
        $objSheet->setCellValue('F8', "Gọi mới");
        $objSheet->setCellValue('G8', "Nguồn");
        $objSheet->setCellValue('I8', "Mục đích");
        $objSheet->setCellValue('K8', "Kết quả (Y/N)");
        $objSheet->setCellValue('M8', "Ngày hẹn");

        $objSheet->mergeCells('B8:C8');
        $objSheet->mergeCells('D8:E8');
        $objSheet->mergeCells('G8:H8');
        $objSheet->mergeCells('K8:L8');
        $objSheet->mergeCells('I8:J8');
        $objSheet->mergeCells('M8:N8');

        $objSheet->getColumnDimension("A")->setWidth(5);
        $objSheet->getColumnDimension("B")->setWidth(10);
        $objSheet->getColumnDimension("C")->setWidth(10);
        $objSheet->getColumnDimension("D")->setWidth(12);
        $objSheet->getColumnDimension("E")->setWidth(5);
        $objSheet->getColumnDimension("F")->setWidth(15);
        $objSheet->getColumnDimension("G")->setWidth(6);
        $objSheet->getColumnDimension("H")->setWidth(9);
        $objSheet->getColumnDimension("I")->setWidth(7);
        $objSheet->getColumnDimension("J")->setWidth(7);
        $objSheet->getColumnDimension("K")->setWidth(7);
        $objSheet->getColumnDimension("L")->setWidth(7);
        $objSheet->getColumnDimension("M")->setWidth(10);
        $objSheet->getColumnDimension("N")->setWidth(16);
        $objSheet->getColumnDimension("O")->setWidth(13);

        $rangeTitleCuocGoi = $objSheet->getStyle("A8:M8");
        $rangeTitleCuocGoi->getFont()->setBold(true);
        $rangeTitleCuocGoi->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        // binding data cuoc goi
        $row = 9;
        $data = $this->getCallResultData(DatetimeUtils::convertStringToDate($date), $user);
        if (count($data) > 0) {

            $stt = 1;
            foreach ($data as $index => $model) {
                $customer = Customer::findOne(['id' => $model->customer_id]);
                $chanel = Chanel::findOne(['id'=>$model->chanel_id]);
                $purpose = Purpose::findOne(['id'=>$model->purpose_id]);
                $objSheet->setCellValue('A' . $row, $stt++);
                $objSheet->setCellValue('B' . $row, $customer->name);
                $objSheet->setCellValue('D' . $row, $customer->phone);
                $objSheet->setCellValue('F' . $row, $model->is_new_call == 1 ?"Yes":"No");
                $objSheet->setCellValue('G' . $row, !is_null($chanel)?$chanel->name:"");
                $objSheet->setCellValue('I' . $row, !is_null($purpose)?$purpose->name:"");
                $objSheet->setCellValue('K' . $row, $model->result == 1 ?"Yes":"No");
                $objSheet->setCellValue('M' . $row, DatetimeUtils::isDatetimeNotEmptyOrNull($model->appointment_date)?DatetimeUtils::formatDate($model->appointment_date,"d/m/Y H:i"):"");

                $objSheet->mergeCells('B'.$row.':C'.$row);
                $objSheet->mergeCells('D'.$row.':E'.$row);
                $objSheet->mergeCells('G'.$row.':H'.$row);
                $objSheet->mergeCells('K'.$row.':L'.$row);
                $objSheet->mergeCells('I'.$row.':J'.$row);
                $objSheet->mergeCells('M'.$row.':N'.$row);

                $objSheet->getStyle('A'.$row)->getAlignment()->setWrapText(true);

                $row++;
            }

        }
        // bao khung các dòng dữ liệu
        $objSheet->getStyle("A8:N" . ($row-1))->applyFromArray(
            array(
                'borders' => array(
                    'allborders' => array(
                        'style' => \PHPExcel_Style_Border::BORDER_THIN,
                        'color' => array('rgb' => '000000')
                    )
                )
            )
        );
        $objSheet->getStyle("A8:N" . $row)->getAlignment()->setWrapText(true);
        $objSheet->getStyle("A8:N" . $row)->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_TOP);

        // binding data meeting result
        $rowMeeting = $row + 1;
        $data_meeting_result = $this->getMeetingResultData(DatetimeUtils::convertStringToDate($date), $user);

        $objSheet->setCellValue('A'.$rowMeeting, "KẾT QUẢ GẶP KHÁCH HÀNG");
        $objSheet->mergeCells('A'.$rowMeeting.':M'.$rowMeeting);
        $rangeTitleMeeting = $objSheet->getStyle("A".$rowMeeting.":N".$rowMeeting);
        $rangeTitleMeeting->getFont()->setBold(true);
        $rangeTitleMeeting->getFont()->setSize(14);


        $rowMeeting = $rowMeeting + 1;

        $objSheet->setCellValue('H'.$rowMeeting, "Kết quả");
        $objSheet->mergeCells('H'.$rowMeeting.':O'.$rowMeeting);


        $objSheet->setCellValue('A'.$rowMeeting, "STT");
        $objSheet->setCellValue('B'.$rowMeeting, "Tên khách hàng");
        $objSheet->setCellValue('D'.$rowMeeting, "SĐT");
        $objSheet->setCellValue('F'.$rowMeeting, "Nguồn");
        $objSheet->setCellValue('G'.$rowMeeting, "Gặp mới");

        $rowMeeting = $rowMeeting + 1;
        $objSheet->setCellValue('H'.$rowMeeting, "HĐ");
        $objSheet->setCellValue('I'.$rowMeeting, "FHC");
        $objSheet->setCellValue('J'.$rowMeeting, "SIS");
        $objSheet->setCellValue('K'.$rowMeeting, "Warm");
        $objSheet->setCellValue('L'.$rowMeeting, "KHTN");
        $objSheet->setCellValue('M'.$rowMeeting, "Khác");
        $objSheet->setCellValue('N'.$rowMeeting, "Follow Up");
        $objSheet->setCellValue('O'.$rowMeeting, "Lý do từ chối");
        $objSheet->mergeCells('A'.($rowMeeting-1).':A'.$rowMeeting);
        $objSheet->mergeCells('B'.($rowMeeting-1).':C'.$rowMeeting);
        $objSheet->mergeCells('D'.($rowMeeting-1).':E'.$rowMeeting);
        $objSheet->mergeCells('F'.($rowMeeting-1).':F'.$rowMeeting);
        $objSheet->mergeCells('G'.($rowMeeting-1).':G'.$rowMeeting);

        $rangeTitle = $objSheet->getStyle("A".($rowMeeting-1).":O".$rowMeeting);
        $rangeTitle->getFont()->setBold(true);
        $rangeTitle->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        $rowMeeting = $rowMeeting + 1;

        if(count($data_meeting_result) > 0){
            $stt = 1;
            foreach ($data_meeting_result as $index => $model) {
                $customer = Customer::findOne(['id' => $model->customer_id]);
                $chanel = Chanel::findOne(['id'=>$model->chanel_id]);

                $objSheet->setCellValue('A' . $rowMeeting, $stt++);
                $objSheet->setCellValue('B' . $rowMeeting, $customer->name);
                $objSheet->setCellValue('D' . $rowMeeting, $customer->phone);
                $objSheet->setCellValue('F' . $rowMeeting, !is_null($chanel)?$chanel->name:"");
                $objSheet->setCellValue('G' . $rowMeeting, $model->is_new_meeting == 1 ?"Yes":"No");
                $objSheet->setCellValue('H' . $rowMeeting, $model->hd == 1 ?"Yes":"No");
                $objSheet->setCellValue('I' . $rowMeeting, $model->fhc == 1 ?"Yes":"No");
                $objSheet->setCellValue('J' . $rowMeeting, $model->sis == 1 ?"Yes":"No");
                $objSheet->setCellValue('K' . $rowMeeting, $model->warm == 1 ?"Yes":"No");
                $objSheet->setCellValue('L' . $rowMeeting, $model->khtn);
                $objSheet->setCellValue('M' . $rowMeeting, $model->other);
                $objSheet->setCellValue('N' . $rowMeeting, DatetimeUtils::isDatetimeNotEmptyOrNull($model->follow_up_date)?DatetimeUtils::formatDate($model->follow_up_date, "d/m/Y H:i"):"");
                $objSheet->setCellValue('O' . $rowMeeting, $model->reject_reason);

                $objSheet->mergeCells('B'.$rowMeeting.':C'.$rowMeeting);
                $objSheet->mergeCells('D'.$rowMeeting.':E'.$rowMeeting);
                $rowMeeting++;
            }
        }
        // bao khung các dòng dữ liệu
        $objSheet->getStyle("A".($row+2).":O" . ($rowMeeting-1))->applyFromArray(
            array(
                'borders' => array(
                    'allborders' => array(
                        'style' => \PHPExcel_Style_Border::BORDER_THIN,
                        'color' => array('rgb' => '000000')
                    )
                )
            )
        );
        $objSheet->getStyle("A".($row+2).":O" . $rowMeeting)->getAlignment()->setWrapText(true);
        $objSheet->getStyle("A".($row+2).":O" . $rowMeeting)->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_TOP);
    }
}