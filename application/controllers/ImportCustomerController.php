<?php
/**
 * Created by PhpStorm.
 * User: phuocnguyen
 * Date: 14/10/2018
 * Time: 3:35 PM
 */

namespace application\controllers;

use application\models\Chanel\Chanel;
use application\models\Customer\Customer;
use application\models\Customer\CustomerUtil;
use application\models\ImportCustomer\ImportCustomerUpload;
use application\models\Job\Job;
use application\models\MaritalStatus\MaritalStatus;
use application\utilities\DatetimeUtils;
use application\utilities\SessionUtils;
use Faker\Provider\Uuid;
use yii\helpers\Url;
use yii\validators\EmailValidator;
use yii\web\Controller;
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Style_Alignment;
use Yii;
use yii\web\UploadedFile;

class ImportCustomerController extends Controller
{
    public function actionIndex()
    {
        Yii::$app->session->remove("import_customer");
        $model_upload = new ImportCustomerUpload();
        $post = Yii::$app->request->post();
        $data = array();
        if ($model_upload->load($post)) {
            $data = $this->saveData($model_upload);
        }

        $session = Yii::$app->session;
        return $this->render('index', [
            'model_upload' => $model_upload,
            'data' => $session->get("import_customer")
        ]);
    }

    public function actionDownloadExampleFile()
    {
        $temp_folder = "temp";
        $fileUrl = "";
        $objPHPExcel = new PHPExcel();
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel2007");
        $objSheet = $objPHPExcel->getActiveSheet();
        $objPHPExcel->getDefaultStyle()->getFont()->setName('Times New Roman');
        $objPHPExcel->getDefaultStyle()->getFont()->setSize(10);

        // Cac cot trong danh sach khach hang
        $objSheet->setCellValue('A1', "STT");
        $objSheet->setCellValue('B1', "Họ tên");
        $objSheet->setCellValue('C1', "Giới tính");
        $objSheet->setCellValue('C1', "Giới tính");
        $objSheet->setCellValue('D1', "Năm sinh");
        $objSheet->setCellValue('E1', "SĐT");
        $objSheet->setCellValue('F1', "Email");
        $objSheet->setCellValue('G1', "Địa chỉ");
        $objSheet->setCellValue('H1', "Phân loại");
        $objSheet->setCellValue('I1', "Tình trạng hôn nhân");
        $objSheet->setCellValue('J1', "Công việc");
        $objSheet->setCellValue('K1', "Nguồn");
        $objSheet->setCellValue('L1', "Thu nhập");
        $objSheet->setCellValue('M1', "HĐ");
        $objSheet->setCellValue('N1', "FHC");
        $objSheet->setCellValue('O1', "SIS");
        $objSheet->getColumnDimension("A")->setWidth(5);
        $objSheet->getColumnDimension("B")->setWidth(25);
        $objSheet->getColumnDimension("C")->setWidth(10);
        $objSheet->getColumnDimension("D")->setWidth(15);
        $objSheet->getColumnDimension("E")->setWidth(20);
        $objSheet->getColumnDimension("F")->setWidth(20);
        $objSheet->getColumnDimension("G")->setWidth(20);
        $objSheet->getColumnDimension("H")->setWidth(20);
        $objSheet->getColumnDimension("I")->setWidth(20);
        $objSheet->getColumnDimension("J")->setWidth(20);
        $objSheet->getColumnDimension("K")->setWidth(20);
        $objSheet->getColumnDimension("L")->setWidth(20);
        $objSheet->getColumnDimension("M")->setWidth(10);
        $objSheet->getColumnDimension("N")->setWidth(10);
        $objSheet->getColumnDimension("O")->setWidth(10);


        $config_sexs = "Nam,Nữ";
        $config_jobs = "";
        $config_chanels = "";
        $config_marial_status = "";
        $jobs = Job::find()->all();
        foreach ($jobs as $index => $job) {
            $config_jobs .= $job->name . ",";
        }
        $marialStatus = MaritalStatus::find()->all();
        foreach ($marialStatus as $index => $item) {
            $config_marial_status .= $item->name . ",";
        }
        $chanels = Chanel::find()->all();
        foreach ($chanels as $index => $chanel) {
            $config_chanels .= $chanel->name . ",";
        }

        $categories = "Cold,Warm,Hot";
        $yes_no = "Yes,No";


        for ($i = 2; $i <= 1000; $i++) {
            $objValidation = $objSheet->getCell("C{$i}")->getDataValidation();
            $this->setDataValidate($objValidation, $config_sexs);

            $objValidation = $objSheet->getCell("H{$i}")->getDataValidation();
            $this->setDataValidate($objValidation, $categories);

            $objValidation = $objSheet->getCell("I{$i}")->getDataValidation();
            $this->setDataValidate($objValidation, $config_marial_status);


            $objValidation = $objSheet->getCell("J{$i}")->getDataValidation();
            $this->setDataValidate($objValidation, $config_jobs);

            $objValidation = $objSheet->getCell("K{$i}")->getDataValidation();
            $this->setDataValidate($objValidation, $config_chanels);

            $objValidation = $objSheet->getCell("M{$i}")->getDataValidation();
            $this->setDataValidate($objValidation, $yes_no);

            $objValidation = $objSheet->getCell("N{$i}")->getDataValidation();
            $this->setDataValidate($objValidation, $yes_no);

            $objValidation = $objSheet->getCell("O{$i}")->getDataValidation();
            $this->setDataValidate($objValidation, $yes_no);
        }
        $rangeTitle = $objSheet->getStyle("A1:O1");
        $rangeTitle->getFont()->setBold(true);
        $rangeTitle->getFont()->setSize(10);
        $rangeTitle->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objSheet->setTitle('Danh sách khách hàng');

        $objPHPExcel->setActiveSheetIndex(0);
        $fileName = "DSKH_" . SessionUtils::getUsername() . ".xlsx";
        $fileUrl = Url::to([$temp_folder . "/" . $fileName]);
        $objWriter->save($temp_folder . DIRECTORY_SEPARATOR . $fileName);
        return $fileUrl;
    }

    public function actionSaveImportCustomer()
    {
        $post = Yii::$app->request->post();
        $session = Yii::$app->session;
        $session_key = "import_customer";

        $data = array();
        if ($session->has($session_key)) {
            $data = $session->get($session_key);
        }
        $result = false;
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $ids = $post["ids"];
            if (count($ids) > 0) {
                foreach ($ids as $id) {
                    $item = $data[$id];
                    if (isset($item)) {
                        $name = $item["name"];
                        $sex = $item["sex"];
                        $dateOfBirth = $item["dateOfBirth"];
                        $phone = $item["phone"];
                        if (!CustomerUtil::isDuplicatePhonenumber($phone)) {
                            $email = $item["email"];
                            $address = $item["address"];
                            $category = $item["category"];
                            $marialStatus = $item["marialStatus"];
                            $job = $item["job"];
                            $chanel = $item["chanel"];
                            $salary = $item["salary"];
                            $hd = $item["hd"];
                            $fhc = $item["fhc"];
                            $sis = $item["sis"];

                            $jobObject = Job::findOne(['name' => $job]);
                            $maritalStatusObject = MaritalStatus::findOne(['name' => $marialStatus]);
                            $chanelObject = Chanel::findOne(['name' => $chanel]);
                            $customer = new Customer();
                            $customer->name = $name;
                            $customer->phone = $phone;
                            $customer->email = $email;
                            $customer->birthday = DatetimeUtils::convertStringToDate($dateOfBirth);
                            $customer->job_id = !is_null($jobObject) ? $jobObject->id : null;
                            $customer->address = $address;
                            $customer->marital_status_id = !is_null($maritalStatusObject) ? $maritalStatusObject->id : null;
                            $customer->sex = $sex == "Nam" ? 1 : 0;
                            $customer->salary = str_replace($salary, ".", "");
                            $customer->user_id = SessionUtils::getUserId();
                            $customer->chanel_id = !is_null($chanelObject) ? $chanelObject->id : null;
                            $customer->hd = $hd;
                            $customer->fhc = $fhc;
                            $customer->sis = $sis;
                            $customer->is_active = 1;
                            $customer->created_at = date('Y-m-d H:i:s');
                            $customer->updated_at = date('Y-m-d H:i:s');
                            $customer->created_by = SessionUtils::getUsername();
                            $customer->updated_by = SessionUtils::getUsername();
                            if ($category == "Hot") {
                                $customer->category = 2;
                            } else if ($category == "Warm") {
                                $customer->category = 1;
                            } elseif ($category == "Cold") {
                                $customer->category = 0;
                            }
                            $customer->disabled = 0;
                            $customer->is_lock_change_category = 0;
                            $customer->save(false);
                        }
                    }
                }
                $transaction->commit();
                $session->remove($session_key);
                $result = true;
            }
        } catch (Exception $e) {
            $transaction->rollBack();
            echo $e->getTraceAsString();
        }
        return $result;
    }

    private function saveData($model_upload)
    {
        $session = Yii::$app->session;
        $filename = $this->uploadFileDinhKem($model_upload);
        $objReader = \PHPExcel_IOFactory::createReader("Excel2007");
        $objPHPExcel = $objReader->load($filename);
        $objWorksheet = $objPHPExcel->getActiveSheet();
        $highestRow = $objWorksheet->getHighestRow(); // e.g. 10
        $arrayData = array();
        $check_sexs = array("Nam", "Nữ", "");
        $check_categories = array("Hot", "Warm", "Cold");
        $check_marial_status = array();
        foreach (MaritalStatus::find()->all() as $index => $value) {
            $check_marial_status[] = $value->name;
        }
        $check_jobs = array();
        foreach (Job::find()->all() as $index => $value) {
            $check_jobs[] = $value->name;
        }
        $check_chanels = array();
        foreach (Chanel::find()->all() as $index => $value) {
            $check_chanels[] = $value->name;
        }

        for ($row = 2; $row <= $highestRow; ++$row) {
            $name = $objWorksheet->getCell('B' . $row)->getValue();
            $sex = $objWorksheet->getCell('C' . $row)->getValue();
            $phone = $objWorksheet->getCell('E' . $row)->getValue();
            $email = $objWorksheet->getCell('F' . $row)->getValue();
            $address = $objWorksheet->getCell('G' . $row)->getValue();
            $category = $objWorksheet->getCell('H' . $row)->getValue();
            $marialStatus = $objWorksheet->getCell('I' . $row)->getValue();
            $job = $objWorksheet->getCell('J' . $row)->getValue();
            $chanel = $objWorksheet->getCell('K' . $row)->getValue();
            $salary = $objWorksheet->getCell('L' . $row)->getValue();
            $hd = $objWorksheet->getCell('M' . $row)->getValue();
            $fhc = $objWorksheet->getCell('N' . $row)->getValue();
            $sis = $objWorksheet->getCell('O' . $row)->getValue();

            $errors = array();

            if (empty($name)) {
                $errors[] = "Tên không được bỏ trống";
            }
            if (empty($phone)) {
                $errors[] = "SĐT không được bỏ trống";
            }

            if (!empty($sex) && !in_array($sex, $check_sexs)) {
                $errors[] = "Sai giới tính";
            }

            if (!empty($category) && !in_array($category, $check_categories)) {
                $errors[] = "Sai phân loại";
            }

            if (!empty($marialStatus) && !in_array($marialStatus, $check_marial_status)) {
                $errors[] = "Sai tình trạng hôn nhân";
            }

            if (!empty($job) && !in_array($job, $check_jobs)) {
                $errors[] = "Sai công việc";
            }
            if (!empty($chanel) && !in_array($chanel, $check_chanels)) {
                $errors[] = "Nguồn sai";
            }

            $cellDateOfBirth = $objWorksheet->getCell('D' . $row);
            $dateOfBirth = $cellDateOfBirth->getValue();
            if (\PHPExcel_Shared_Date::isDateTime($cellDateOfBirth)) {
                $dateOfBirth = date('d/m/Y', \PHPExcel_Shared_Date::ExcelToPHP($dateOfBirth));
            }

            if (CustomerUtil::isDuplicatePhonenumber($phone)) {
                $errors[] = "Số điện thoại đã có";
            }

            if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Địa chỉ email không đúng";
            }


            $arrayData[Uuid::uuid()] = array(
                "name" => $name,
                "sex" => $sex,
                "dateOfBirth" => $dateOfBirth,
                "phone" => $phone,
                "email" => $email,
                "address" => $address,
                "category" => $category,
                "marialStatus" => $marialStatus,
                "job" => $job,
                "chanel" => $chanel,
                "salary" => $salary,
                "hd" => $hd,
                "fhc" => $fhc,
                "sis" => $sis,
                "errors" => $errors
            );
        }
        $session_key = "import_customer";
        $session->set($session_key, $arrayData);
        return $session->get($session_key);
    }

    private function setDataValidate($objValidation, $configs)
    {
        $objValidation->setType(\PHPExcel_Cell_DataValidation::TYPE_LIST);
        $objValidation->setErrorStyle(\PHPExcel_Cell_DataValidation::STYLE_INFORMATION);
        $objValidation->setAllowBlank(true);
        $objValidation->setShowInputMessage(true);
        $objValidation->setShowErrorMessage(true);
        $objValidation->setShowDropDown(true);
        $objValidation->setErrorTitle('Input error');
        $objValidation->setError('Dữ liệu bạn chọn không có trong danh sách.');
        $objValidation->setPromptTitle('Chọn dữ liệu từ danh sách');
        $objValidation->setPrompt('Vui lòng chọn dữ liệu từ danh sách đang có.');
        $objValidation->setFormula1('"' . $configs . '"');
    }

    private function uploadFileDinhKem($model_upload)
    {
        if (!is_null($model_upload)) {
            $model_upload->filedinhkem = UploadedFile::getInstance($model_upload, 'filedinhkem');
            if (is_null($model_upload->filedinhkem)) {
                return "";
            }
            $upload_folder = Yii::getAlias('@webroot') . "/temp/uploads";
            $filename = SessionUtils::getUsername() . '.' . $model_upload->filedinhkem->extension;
            $filePath = $upload_folder . "/" . $filename;
            if ($model_upload->filedinhkem->saveAs($filePath)) {
                return $filePath;
            }
        }
        return "";
    }
}