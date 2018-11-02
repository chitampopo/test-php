<?php
/**
 * Created by PhpStorm.
 * User: phuocnguyen
 * Date: 20/09/2018
 * Time: 8:54 PM
 */

namespace application\controllers;


use application\models\Chanel\ChanelUtil;
use application\models\Job\JobUtil;
use application\models\MeetingResult\MeetingResult;
use application\models\SisAnalysis\SisAnalysis;
use application\models\User\User;
use application\models\User\UserUtil;
use application\utilities\DatetimeUtils;
use application\utilities\SessionUtils;
use yii\helpers\Url;
use yii\web\Controller;
use Yii;
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Style_Alignment;

class SisAnalysisController extends Controller
{
    public function actionIndex()
    {
        $model = new SisAnalysis();
        $model->from_date = DatetimeUtils::getFirstDayOfMonthDateDependOnDevice();
        $model->to_date = DatetimeUtils::getCurrentDateDependOnDevice();
        return $this->render('index', [
            'model' => $model
        ]);
    }

    private function getRangeExcelColumn($index)
    {
        $array = array(
            0 => 'A',
            1 => 'B',
            2 => 'C',
            3 => 'D',
            4 => 'E',
            5 => 'F',
            6 => 'G',
            7 => 'H',
            8 => 'I',
            9 => 'J',
            10 => 'K',
            11 => 'L',
            12 => 'M',
            13 => 'N',
            14 => 'O',
            15 => 'P',
            16 => 'Q',
            17 => 'R',
            18 => 'S',
            19 => 'T',
            20 => 'U',
            21 => 'V',
            22 => 'W',
            23 => 'X',
            24 => 'Y',
            25 => 'Z',
            26 => 'AA',
            27 => 'AB',
            28 => 'AC',
            29 => 'AD',
            30 => 'AE',
            31 => 'AF',
        );
        return $array[$index];
    }

    public function actionExportExcel()
    {
        $temp_folder = "temp";
        $fileUrl = "";
        $post = Yii::$app->request->post();
        if (isset($post)) {
            $from_date = isset($post['from_date']) ? $post['from_date'] : DatetimeUtils::getFirstDayOfMonthDateDependOnDevice();
            $to_date = isset($post['to_date']) ? $post['to_date'] : DatetimeUtils::getCurrentDateDependOnDevice();

            $objPHPExcel = new PHPExcel();

            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel2007");

            $objPHPExcel->getDefaultStyle()->getFont()->setName('Times New Roman');
            $objPHPExcel->getDefaultStyle()->getFont()->setSize(12);
            $objSheet = $objPHPExcel->getActiveSheet();
            $xpmUsers = UserUtil::getXpmUsers();
            $chanels = ChanelUtil::getChanels();
            $jobs = JobUtil::getJobs();
            if (count($xpmUsers) > 0) {
                foreach ($xpmUsers as $index => $xpmUser) {
                    $objSheet->setTitle($xpmUser->name);
                    $this->createSheetData($objSheet, $xpmUser, $from_date, $to_date, $chanels, $jobs);
                    $objSheet = $objPHPExcel->createSheet();
                }
                $this->createSheetHeader($objSheet, $from_date, $to_date, $chanels, $jobs);
                $row = 7;
                $fistRowData=$row;
                $stt = 1;
                $totalColumn = count($chanels) + count($jobs) + 4 + 3 + 4 + 3;
                foreach ($xpmUsers as $index => $xpmUser) {
                    $objSheet->setCellValue('A' . $row, $stt++);
                    $objSheet->setCellValue('B' . $row, $xpmUser->name);
                    for ($i = 3; $i <= $totalColumn; $i++) {
                        $countUsers = User::find()->orderBy(['name' => SORT_ASC])->andWhere(['department_id' => $xpmUser->department_id])->count();
                        $letterColumn = $this->getRangeExcelColumn($i);
                        $cell = $letterColumn . ($countUsers + 7);
                        $objSheet->setCellValue('C' . $row, "='{$xpmUser->name}'!".$cell);
                        $objSheet->setCellValue($letterColumn . $row, "='{$xpmUser->name}'!".$cell);
                    }
                    $row++;
                }
                $objSheet->setCellValue("A" . $row, "TỔNG CỘNG");
                $sumRow = $row - 1;
                $objSheet->setCellValue("C" . $row, "=SUM(C" . $fistRowData . ":C" . $sumRow . ")");
                $colIndex = 3;
                for ($i = 0; $i < $totalColumn-2; $i++) {
                    $letterSum = $this->getRangeExcelColumn($colIndex + $i);
                    $objSheet->setCellValue($letterSum . $row, "=SUM({$letterSum}" . $fistRowData . ":{$letterSum}" . $sumRow . ")");
                }
                $lastColumnLetter = $this->getRangeExcelColumn($totalColumn);
                $objSheet->mergeCells('A' . $row . ':B' . $row);
                $objSheet->getStyle("A5:" . $lastColumnLetter . $row)->applyFromArray(
                    array(
                        'borders' => array(
                            'allborders' => array(
                                'style' => \PHPExcel_Style_Border::BORDER_THIN,
                                'color' => array('rgb' => '000000')
                            )
                        )
                    )
                );
                $rangeTotal = $objSheet->getStyle("A{$row}:" . $lastColumnLetter . $row);
                $rangeTotal->getFont()->setBold(true);
                $objSheet->setTitle("TỔNG CỘNG");
            }
            $objPHPExcel->setActiveSheetIndex(0);

            $fileName = "SIS_ANALYSIS_" . SessionUtils::getUsername() . ".xlsx";
            $fileUrl = Url::to([$temp_folder . "/" . $fileName]);
            $objWriter->save($temp_folder . DIRECTORY_SEPARATOR . $fileName);
        }
        return $fileUrl;
    }

    private function createSheetHeader($objSheet, $from_date, $to_date, $chanels, $jobs)
    {
        // set tieu de bao cao
        $objSheet->setCellValue('A1', "PHÂN TÍCH KHÁCH HÀNG CHƯA THAM GIA SIS");
        $objSheet->setCellValue('A2', "Từ ngày {$from_date} đến ngày {$to_date}");
        // set tên các cột
        $objSheet->setCellValue('A5', "STT");
        $objSheet->setCellValue('B5', "XP/SXP/EXP");
        $objSheet->setCellValue('C5', "Số lượng");
        $objSheet->setCellValue('D5', "Nguồn KH");

        $objSheet->mergeCells('A5:A6');
        $objSheet->mergeCells('B5:B6');
        $objSheet->mergeCells('C5:C6');

        $colIndex = 3;
        if (count($chanels) > 0) {
            foreach ($chanels as $index => $chanel) {
                $letterChanel = $this->getRangeExcelColumn($colIndex);
                $objSheet->setCellValue($letterChanel . "6", $chanel->name);
                $objSheet->getColumnDimension($letterChanel)->setWidth(7);
                $colIndex++;
            }
        }
        $objSheet->mergeCells('D5:' . $this->getRangeExcelColumn($colIndex - 1) . "5");

        $beginColumnDoTuoi = $this->getRangeExcelColumn($colIndex) . "5";
        $objSheet->setCellValue($beginColumnDoTuoi, "Độ tuổi");
        $objSheet->getColumnDimension($this->getRangeExcelColumn($colIndex))->setWidth(6);
        $objSheet->setCellValue($this->getRangeExcelColumn($colIndex++) . '6', "20-25");
        $objSheet->getColumnDimension($this->getRangeExcelColumn($colIndex))->setWidth(6);
        $objSheet->setCellValue($this->getRangeExcelColumn($colIndex++) . '6', "26-30");
        $objSheet->getColumnDimension($this->getRangeExcelColumn($colIndex))->setWidth(6);
        $objSheet->setCellValue($this->getRangeExcelColumn($colIndex++) . '6', "31-45");
        $objSheet->getColumnDimension($this->getRangeExcelColumn($colIndex))->setWidth(6);
        $objSheet->setCellValue($this->getRangeExcelColumn($colIndex++) . '6', "46-55");
        $objSheet->getColumnDimension($this->getRangeExcelColumn($colIndex))->setWidth(6);
        $objSheet->mergeCells($beginColumnDoTuoi . ':' . $this->getRangeExcelColumn($colIndex - 1) . "5");

        $beginColumnGiaDinh = $this->getRangeExcelColumn($colIndex) . "5";
        $objSheet->setCellValue($beginColumnGiaDinh, "Gia Đình");
        $objSheet->getColumnDimension($this->getRangeExcelColumn($colIndex))->setWidth(4);
        $objSheet->setCellValue($this->getRangeExcelColumn($colIndex++) . '6', "S");
        $objSheet->getColumnDimension($this->getRangeExcelColumn($colIndex))->setWidth(4);
        $objSheet->setCellValue($this->getRangeExcelColumn($colIndex++) . '6', "M");
        $objSheet->getColumnDimension($this->getRangeExcelColumn($colIndex))->setWidth(4);
        $objSheet->setCellValue($this->getRangeExcelColumn($colIndex++) . '6', "C");
        $objSheet->getColumnDimension($this->getRangeExcelColumn($colIndex))->setWidth(4);
        $objSheet->mergeCells($beginColumnGiaDinh . ':' . $this->getRangeExcelColumn($colIndex - 1) . "5");


        $beginColumnThuNhap = $this->getRangeExcelColumn($colIndex) . "5";
        $objSheet->getColumnDimension($this->getRangeExcelColumn($colIndex))->setWidth(4);
        $objSheet->setCellValue($beginColumnThuNhap, "Thu nhập");
        $objSheet->setCellValue($this->getRangeExcelColumn($colIndex++) . '6', "<10");
        $objSheet->getColumnDimension($this->getRangeExcelColumn($colIndex))->setWidth(4);
        $objSheet->setCellValue($this->getRangeExcelColumn($colIndex++) . '6', "<20");
        $objSheet->getColumnDimension($this->getRangeExcelColumn($colIndex))->setWidth(4);
        $objSheet->setCellValue($this->getRangeExcelColumn($colIndex++) . '6', "<30");
        $objSheet->getColumnDimension($this->getRangeExcelColumn($colIndex))->setWidth(4);
        $objSheet->setCellValue($this->getRangeExcelColumn($colIndex++) . '6', ">30");
        $objSheet->getColumnDimension($this->getRangeExcelColumn($colIndex))->setWidth(4);
        $objSheet->mergeCells($beginColumnThuNhap . ':' . $this->getRangeExcelColumn($colIndex - 1) . "5");

        $beginColumnNgheNghiep = $this->getRangeExcelColumn($colIndex) . "5";
        $objSheet->getColumnDimension($this->getRangeExcelColumn($colIndex))->setWidth(20);
        $objSheet->setCellValue($beginColumnNgheNghiep, "Nghề nghiệp");
        if (count($jobs) > 0) {
            foreach ($jobs as $index => $job) {
                $letterChanel = $this->getRangeExcelColumn($colIndex);
                $objSheet->setCellValue($letterChanel . "6", $job->name);
                $objSheet->getColumnDimension($letterChanel)->setWidth(7);
                $colIndex++;
            }
        }
        $objSheet->mergeCells($beginColumnNgheNghiep . ':' . $this->getRangeExcelColumn($colIndex - 1) . "5");
        $objSheet->getColumnDimension("A")->setWidth(5);
        $objSheet->getColumnDimension("B")->setWidth(25);
        $objSheet->getColumnDimension("C")->setWidth(9);

        $lastColumnLetter = $this->getRangeExcelColumn($colIndex - 1);
        $objSheet->mergeCells('A1:' . $lastColumnLetter . '1');
        $objSheet->mergeCells('A2:' . $lastColumnLetter . '2');

        // Format tiêu đề
        $rangeTitle = $objSheet->getStyle("A1:" . $lastColumnLetter . "1");
        $rangeTitle->getFont()->setBold(true);
        $rangeTitle->getFont()->setSize(18);
        $rangeTitle->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $rangeTitle1 = $objSheet->getStyle("A2:" . $lastColumnLetter . "2");
        $rangeTitle1->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        // Format tiêu đề
        $rangeTitleTable = $objSheet->getStyle("A5:" . $lastColumnLetter . "6");
        $rangeTitleTable->getAlignment()->setWrapText(true);
        $rangeTitleTable->getFont()->setBold(true);
        $rangeTitleTable->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        return $lastColumnLetter;
    }

    private function createSheetData($objSheet, $xpmUser, $from_date, $to_date, $chanels, $jobs)
    {
        $lastColumnLetter = $this->createSheetHeader($objSheet, $from_date, $to_date, $chanels, $jobs);
        $fromDate = DatetimeUtils::convertStringToDateTime($from_date, 0, 0);
        $toDate = DatetimeUtils::convertStringToDateTime($to_date, 23, 59);
        $users = UserUtil::getUserByDepartment($xpmUser->department_id);
        $fistRowData = 7;
        $row = $fistRowData;
        if (count($users) > 0) {
            $stt = 1;
            foreach ($users as $index => $user) {
                $objSheet->setCellValue('A' . $row, $stt++);
                $objSheet->setCellValue('B' . $row, $user->name);
                $colIndex = 3;
                $soLuong = MeetingResult::find()
                    ->andWhere(['user_id' => $user->id])
                    ->andWhere(['sis' => 1])
                    ->andWhere(['hd' => 0])
                    ->andWhere("meeting_date between '{$fromDate}' and '{$toDate}'")
                    ->count();
                $objSheet->setCellValue('C' . $row, ($soLuong > 0) ? $soLuong : "");
                foreach ($chanels as $index => $chanel) {
                    $soLuongChanel = MeetingResult::find()
                        ->andWhere(['user_id' => $user->id])
                        ->andWhere(['sis' => 1])
                        ->andWhere(['hd' => 0])
                        ->andWhere(['chanel_id' => $chanel->id])
                        ->andWhere("meeting_date between '{$fromDate}' and '{$toDate}'")
                        ->count();
                    $letterChanel = $this->getRangeExcelColumn($colIndex);
                    $objSheet->setCellValue($letterChanel . $row, $soLuongChanel > 0 ? $soLuongChanel : "");
                    $colIndex++;
                }

                $soLuongDoTuoi20_25 = MeetingResult::find()
                    ->join('inner join ', 'customer', 'customer_id=customer.id')
                    ->andWhere(['meeting_result.user_id' => $user->id])
                    ->andWhere(['sis' => 1])
                    ->andWhere(['hd' => 0])
                    ->andWhere("meeting_date between '{$fromDate}' and '{$toDate}'")
                    ->andWhere("year(now()) - year(customer.birthday) >=20 and year(now()) - year(customer.birthday)<=25")
                    ->count();

                $objSheet->setCellValue($this->getRangeExcelColumn($colIndex++) . $row, $soLuongDoTuoi20_25 > 0 ? $soLuongDoTuoi20_25 : "");

                $soLuongDoTuoi26_30 = MeetingResult::find()
                    ->join('inner join ', 'customer', 'customer_id=customer.id')
                    ->andWhere(['meeting_result.user_id' => $user->id])
                    ->andWhere(['sis' => 1])
                    ->andWhere(['hd' => 0])
                    ->andWhere("meeting_date between '{$fromDate}' and '{$toDate}'")
                    ->andWhere("year(now()) - year(customer.birthday) >=26 and year(now()) - year(customer.birthday)<=30")
                    ->count();

                $objSheet->setCellValue($this->getRangeExcelColumn($colIndex++) . $row, $soLuongDoTuoi26_30 > 0 ? $soLuongDoTuoi26_30 : "");

                $soLuongDoTuoi31_45 = MeetingResult::find()
                    ->join('inner join ', 'customer', 'customer_id=customer.id')
                    ->andWhere(['meeting_result.user_id' => $user->id])
                    ->andWhere(['sis' => 1])
                    ->andWhere(['hd' => 0])
                    ->andWhere("meeting_date between '{$fromDate}' and '{$toDate}'")
                    ->andWhere("year(now()) - year(customer.birthday) >=31 and year(now()) - year(customer.birthday)<=45")
                    ->count();

                $objSheet->setCellValue($this->getRangeExcelColumn($colIndex++) . $row, $soLuongDoTuoi31_45 > 0 ? $soLuongDoTuoi31_45 : "");

                $soLuongDoTuoi46_55 = MeetingResult::find()
                    ->join('inner join ', 'customer', 'customer_id=customer.id')
                    ->andWhere(['meeting_result.user_id' => $user->id])
                    ->andWhere(['sis' => 1])
                    ->andWhere(['hd' => 0])
                    ->andWhere("meeting_date between '{$fromDate}' and '{$toDate}'")
                    ->andWhere("year(now()) - year(customer.birthday) >=46 and year(now()) - year(customer.birthday)<=55")
                    ->count();

                $objSheet->setCellValue($this->getRangeExcelColumn($colIndex++) . $row, $soLuongDoTuoi46_55 > 0 ? $soLuongDoTuoi46_55 : "");

                $soLuongHonNhanDocThan = MeetingResult::find()
                    ->join('inner join ', 'customer', 'customer_id=customer.id')
                    ->andWhere(['meeting_result.user_id' => $user->id])
                    ->andWhere(['sis' => 1])
                    ->andWhere(['hd' => 0])
                    ->andWhere("meeting_date between '{$fromDate}' and '{$toDate}'")
                    ->andWhere(["customer.marital_status_id" => 1])
                    ->count();

                $objSheet->setCellValue($this->getRangeExcelColumn($colIndex++) . $row, $soLuongHonNhanDocThan > 0 ? $soLuongHonNhanDocThan : "");

                $soLuongHonNhanKhac = MeetingResult::find()
                    ->join('inner join ', 'customer', 'customer_id=customer.id')
                    ->andWhere(['meeting_result.user_id' => $user->id])
                    ->andWhere(['sis' => 1])
                    ->andWhere(['hd' => 0])
                    ->andWhere("meeting_date between '{$fromDate}' and '{$toDate}'")
                    ->andWhere("customer.marital_status_id<>1")
                    ->count();

                $objSheet->setCellValue($this->getRangeExcelColumn($colIndex++) . $row, $soLuongHonNhanKhac > 0 ? $soLuongHonNhanKhac : "");

                $soLuongCon = MeetingResult::find()
                    ->join('inner join ', 'customer', 'customer_id=customer.id')
                    ->andWhere(['meeting_result.user_id' => $user->id])
                    ->andWhere(['sis' => 1])
                    ->andWhere(['hd' => 0])
                    ->andWhere("meeting_date between '{$fromDate}' and '{$toDate}'")
                    ->andWhere(['>', 'customer.number_of_children', 0])
                    ->count();

                $objSheet->setCellValue($this->getRangeExcelColumn($colIndex++) . $row, $soLuongCon > 0 ? $soLuongCon : "");

                $soLuongThuNhapNhoHon10tr = MeetingResult::find()
                    ->join('inner join ', 'customer', 'customer_id=customer.id')
                    ->andWhere(['meeting_result.user_id' => $user->id])
                    ->andWhere(['sis' => 1])
                    ->andWhere(['hd' => 0])
                    ->andWhere("meeting_date between '{$fromDate}' and '{$toDate}'")
                    ->andWhere("customer.salary >0 and customer.salary < 10000000")
                    ->count();

                $objSheet->setCellValue($this->getRangeExcelColumn($colIndex++) . $row, $soLuongThuNhapNhoHon10tr > 0 ? $soLuongThuNhapNhoHon10tr : "");

                $soLuongThuNhapNhoHon20tr = MeetingResult::find()
                    ->join('inner join ', 'customer', 'customer_id=customer.id')
                    ->andWhere(['meeting_result.user_id' => $user->id])
                    ->andWhere(['sis' => 1])
                    ->andWhere(['hd' => 0])
                    ->andWhere("meeting_date between '{$fromDate}' and '{$toDate}'")
                    ->andWhere("customer.salary >= 10000000 and customer.salary < 20000000")
                    ->count();
                $objSheet->setCellValue($this->getRangeExcelColumn($colIndex++) . $row, $soLuongThuNhapNhoHon20tr > 0 ? $soLuongThuNhapNhoHon20tr : "");

                $soLuongThuNhapNhoHon30tr = MeetingResult::find()
                    ->join('inner join ', 'customer', 'customer_id=customer.id')
                    ->andWhere(['meeting_result.user_id' => $user->id])
                    ->andWhere(['sis' => 1])
                    ->andWhere(['hd' => 0])
                    ->andWhere("meeting_date between '{$fromDate}' and '{$toDate}'")
                    ->andWhere("customer.salary >= 20000000 and customer.salary < 30000000")
                    ->count();
                $objSheet->setCellValue($this->getRangeExcelColumn($colIndex++) . $row, $soLuongThuNhapNhoHon30tr > 0 ? $soLuongThuNhapNhoHon30tr : "");

                $soLuongThuNhapHon30tr = MeetingResult::find()
                    ->join('inner join ', 'customer', 'customer_id=customer.id')
                    ->andWhere(['meeting_result.user_id' => $user->id])
                    ->andWhere(['sis' => 1])
                    ->andWhere(['hd' => 0])
                    ->andWhere("meeting_date between '{$fromDate}' and '{$toDate}'")
                    ->andWhere("customer.salary >= 30000000")
                    ->count();
                $objSheet->setCellValue($this->getRangeExcelColumn($colIndex++) . $row, $soLuongThuNhapHon30tr > 0 ? $soLuongThuNhapHon30tr : "");

                foreach ($jobs as $index => $job) {
                    $soLuongJob = MeetingResult::find()
                        ->join('inner join ', 'customer', 'customer_id=customer.id')
                        ->andWhere(['meeting_result.user_id' => $user->id])
                        ->andWhere(['sis' => 1])
                        ->andWhere(['hd' => 0])
                        ->andWhere(['customer.job_id' => $job->id])
                        ->andWhere("meeting_date between '{$fromDate}' and '{$toDate}'")
                        ->count();
                    $letterChanel = $this->getRangeExcelColumn($colIndex);
                    $objSheet->setCellValue($letterChanel . $row, $soLuongJob > 0 ? $soLuongJob : "");
                    $colIndex++;
                }

                $row++;
            }
        }
        $objSheet->setCellValue("A" . $row, "TỔNG CỘNG");
        $sumRow = $row - 1;
        $objSheet->setCellValue("C" . $row, "=SUM(C" . $fistRowData . ":C" . $sumRow . ")");
        $colIndex = 3;
        for ($i = 0; $i < (7 + 4 + count($chanels) + count($jobs)); $i++) {
            $letterSum = $this->getRangeExcelColumn($colIndex + $i);
            $objSheet->setCellValue($letterSum . $row, "=SUM({$letterSum}" . $fistRowData . ":{$letterSum}" . $sumRow . ")");
        }

        $rangeTotal = $objSheet->getStyle("A{$row}:" . $lastColumnLetter . $row);
        $rangeTotal->getFont()->setBold(true);

        $objSheet->mergeCells('A' . $row . ':B' . $row);
        $objSheet->getStyle("A5:" . $lastColumnLetter . $row)->applyFromArray(
            array(
                'borders' => array(
                    'allborders' => array(
                        'style' => \PHPExcel_Style_Border::BORDER_THIN,
                        'color' => array('rgb' => '000000')
                    )
                )
            )
        );
    }
}