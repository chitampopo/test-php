<?php
/**
 * Created by PhpStorm.
 * User: phuocnguyen
 * Date: 17/09/2018
 * Time: 7:40 PM
 */

namespace application\controllers;


use application\models\Chanel\Chanel;
use application\models\Customer\Customer;
use application\models\Department\Department;
use application\models\Department\DepartmentUtil;
use application\models\DepartmentShedule\DepartmentShedule;
use application\models\JfwSchedule\JfwSchedule;
use application\models\Purpose\Purpose;
use application\models\User\User;
use application\models\PersonalSchedule\PersonalSchedule;
use application\utilities\DatetimeUtils;
use application\utilities\SessionUtils;
use yii\helpers\Url;
use yii\web\Controller;
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Style_Alignment;
use Yii;
class DepartmentSheduleController extends Controller
{
    public function actionIndex(){
        $model = new DepartmentShedule();
        $model->date = DatetimeUtils::getCurrentDateDependOnDevice();
        return $this->render('index', [
            'model' => $model,
            'departments'=>DepartmentUtil::getDropdownList()
        ]);
    }

    public function actionExportExcel()
    {
        $temp_folder = "temp";
        $fileUrl = "";
        $post = Yii::$app->request->post();
        if (isset($post)) {

            $date = isset($post['date']) ? $post['date'] : DatetimeUtils::getFirstDayOfMonthDateDependOnDevice();
            $department_id = isset($post['department_id']) ? $post['department_id'] : "";

            $objPHPExcel = new PHPExcel();

            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel2007");
            $objSheet = $objPHPExcel->getActiveSheet();
            $objPHPExcel->getDefaultStyle()->getFont()->setName('Times New Roman');
            $objPHPExcel->getDefaultStyle()->getFont()->setSize(12);

            // set tên các cột
            $objSheet->setCellValue('A4', "STT");
            $objSheet->setCellValue('B4', "Nhân viên");
            $objSheet->setCellValue('C4', "Gặp/Gọi");
            $objSheet->setCellValue('D4', "Giờ");
            $objSheet->setCellValue('E4', "Khách hàng");
            $objSheet->setCellValue('F4', "Điện thoại");
            $objSheet->setCellValue('G4', "Mới");
            $objSheet->setCellValue('H4', "Nguồn");
            $objSheet->setCellValue('I4', "Mục đích");
            $objSheet->setCellValue('J4', "JFW");

            $rangeTitleTable = $objSheet->getStyle("A4:J4");
            $rangeTitleTable->getFont()->setBold(true);
            $rangeTitleTable->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            // Set kích thước các cột
            $objSheet->getColumnDimension("A")->setWidth(5);
            $objSheet->getColumnDimension("B")->setWidth(25);
            $objSheet->getColumnDimension("C")->setWidth(9);
            $objSheet->getColumnDimension("D")->setWidth(7);
            $objSheet->getColumnDimension("E")->setWidth(25);
            $objSheet->getColumnDimension("F")->setWidth(15);
            $objSheet->getColumnDimension("G")->setWidth(7);
            $objSheet->getColumnDimension("H")->setWidth(20);
            $objSheet->getColumnDimension("I")->setWidth(15);
            $objSheet->getColumnDimension("J")->setWidth(7);

            $department = null;
            $queryUser = User::find()->andWhere(['is_active'=>1]);
            if(!empty($department_id)){
                $department = Department::findOne(['id'=>$department_id]);
                $queryUser->andWhere(['department_id' => $department_id]);
            }
            $queryUser->orderBy(['name' => SORT_ASC]);
            $users = $queryUser->all();
            if(!is_null($department)) {
                $objSheet->setCellValue('A1', "Lịch làm việc " . $department->name);
            }else{
                $objSheet->setCellValue('A1', "LỊCH LÀM VIỆC");
            }
            $objSheet->mergeCells('A1:J1');
            $rangeTitle = $objSheet->getStyle("A1:J1");
            $rangeTitle->getFont()->setBold(true);
            $rangeTitle->getFont()->setSize(18);
            $rangeTitle->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            $objSheet->setCellValue('A2', "Ngày: ".$date);
            $objSheet->mergeCells('A2:J2');

            $rangeTitleNgay = $objSheet->getStyle("A2:J2");
            $rangeTitleNgay->getFont()->setBold(true);
            $rangeTitleNgay->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);


            // binding data
            if (count($users) > 0) {
                $row = 5;
                $stt = 1;
                foreach ($users as $index => $user) {
                    $schedules = $this->getSchedule($user->id, DatetimeUtils::convertStringToDate($date));
                    $objSheet->setCellValue('A' . $row, $stt++);
                    $objSheet->setCellValue('B' . $row, $user->name);
                    if(count($schedules)>0){
                        foreach ($schedules as $index => $schedule) {
                            $customer = Customer::findOne(['id'=>$schedule->customer_id]);
                            $chanel = Chanel::findOne(['id'=>$schedule->chanel_id]);
                            $purpose = Purpose::findOne(['id'=>$schedule->purpose_id]);
                            $objSheet->setCellValue('C' . $row, $schedule->is_call?"Gọi":"Gặp");
                            $objSheet->setCellValue('D' . $row, DatetimeUtils::formatDate($schedule->date,'H:i'));
                            $objSheet->setCellValue('E' . $row, !is_null($customer)?$customer->name:"");
                            $objSheet->setCellValue('F' . $row, !is_null($customer)?$customer->phone:"");
                            $objSheet->setCellValue('G' . $row, $schedule->is_new_customer==1 ? "Yes":"No");
                            $objSheet->setCellValue('H' . $row, !is_null($chanel)?$chanel->name:"");
                            $objSheet->setCellValue('I' . $row, !is_null($purpose)?$purpose->name:"");

                            $hasJfw = JfwSchedule::find()->andWhere(['xp_schedule_id'=>$schedule->id])->count();
                            $objSheet->setCellValue('J' . $row, $hasJfw>0?'Yes':'No');
                            $row++;
                        }
                        $objSheet->mergeCells('B'.($row-(count($schedules))).':B'.($row-1));
                        $objSheet->mergeCells('A'.($row-(count($schedules))).':A'.($row-1));
                    }else{
                        $row++;
                    }
                }

                // bao khung các dòng dữ liệu
                $objSheet->getStyle("A4:J" . ($row-1))->applyFromArray(
                    array(
                        'borders' => array(
                            'allborders' => array(
                                'style' => \PHPExcel_Style_Border::BORDER_THIN,
                                'color' => array('rgb' => '000000')
                            )
                        )
                    )
                );
                $objSheet->getStyle("A4:J" . $row)->getAlignment()->setWrapText(true);
                $objSheet->getStyle("A4:J" . ($row-1))->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_TOP);
            }

            $objSheet->setTitle('LỊCH LÀM VIỆC');

            $objPHPExcel->setActiveSheetIndex(0);
            $fileName = "WorkingSchedule_" . SessionUtils::getUsername() .".xlsx";
            $fileUrl = Url::to([$temp_folder . "/" . $fileName]);
            $objWriter->save($temp_folder . DIRECTORY_SEPARATOR . $fileName);
        }
        return $fileUrl;
    }

    private function getSchedule($userId, $date){
        return PersonalSchedule::find()
            ->andWhere(['user_id'=>$userId])
            ->andWhere(["date_format(date,'%Y-%m-%d')" => $date])
            ->orderBy(['date'=>SORT_ASC])
            ->all();
    }
}