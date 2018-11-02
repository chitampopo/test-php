<?php

namespace application\controllers;

use application\models\Chanel\ChanelUtil;
use application\models\Chanel\Chanel;
use application\models\CallResult\CallResultUtil;
use application\models\Customer\CategoryUtil;
use application\models\Department\DepartmentUtil;
use application\models\Job\Job;
use application\models\Job\JobUtil;
use application\models\MeetingResult\MeetingResultUtil;
use application\models\User\UserInfo;
use application\models\Customer\Customer;
use application\models\Customer\CustomerSearch;
use application\models\Customer\SexUtil;
use application\models\MaritalStatus\MaritalStatusUtil;
use application\models\User\UserUtil;
use application\utilities\DatetimeUtils;
use application\utilities\DeleteDataUtil;
use application\utilities\MessageUtils;
use application\utilities\NumberUtils;
use application\utilities\PermissionUtil;
use application\utilities\SessionUtils;
use application\utilities\UrlUtils;
use yii\data\ActiveDataProvider;
use yii\helpers\Url;
use yii\web\Controller;
use Yii;
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Style_Alignment;

class CustomerController extends Controller
{
    public function beforeAction($action)
    {
        PermissionUtil::canAccess('customer');
        return parent::beforeAction($action);
    }

    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Customer::find()
        ]);
        $customerSearch = new CustomerSearch();
        $params = Yii::$app->request->get();
        $data = $customerSearch->search($params);
        $users = UserUtil::getDropdownList(false);
        $departments = DepartmentUtil::getDepartments();
        if (!PermissionUtil::isXPMRole() && !PermissionUtil::isXPRole()) {
            if (isset($params["CustomerSearch"])) {
                $getFhcReportSearch = $params["CustomerSearch"];
                $department_id = isset($getFhcReportSearch["department_id"]) ? $getFhcReportSearch["department_id"] : "";
                if (!empty($department_id)) {
                    $users = UserUtil::getDropdownListRelatedToUsers(false, UserUtil::getUserByDepartment($department_id));
                }
            }
        }
        $model = new Customer();
        return $this->render('index', [
            'model' => $model,
            'dataProvider' => $dataProvider,
            'customerSearch' => $customerSearch,
            'users' => $users,
            'data' => $data,
            'categories' => CategoryUtil::getDropDownList(false),
            'departments' => $departments
        ]);
    }

    public function actionCreate()
    {
        $model = new Customer();
        $post = Yii::$app->request->post();

        if ($model->load($post)) {
            if ($model->validate()) {
                $model->salary = (int)str_replace(',', '', $model->salary);
                $result = $model->save();
                $lastId = Yii::$app->db->getLastInsertID();
                if ($result && UrlUtils::isGoBack()) {
                    return $this->redirect([UrlUtils::buildGoBackUrl($lastId)]);
                }
            }
        }
        $backUrl = UrlUtils::buildGoBackUrl();
        return $this->render('update', [
            'model' => $model,
            'maritalStatus' => MaritalStatusUtil::getDropDownList(false),
            'sex' => SexUtil::getDropdownList(false),
            'channels' => ChanelUtil::getDropdownList(false),
            'backUrl' => !empty($backUrl) ? $backUrl : '/customer/',
            'postUrl' => UrlUtils::buildPostUrl('customer', 'create'),
            'categories' => CategoryUtil::getDropDownList(false),
            'jobs' => JobUtil::getDropdownList(false)
        ]);
    }

    public function actionUpdate($id = null)
    {
        $customer = new Customer();
        if (UrlUtils::isEditAction($id)) {
            $customer = Customer::findOne(['id' => $id]);
            if ($customer->disabled == 1) {
                $this->redirect(['/customer']);
            }
        }
        $post = Yii::$app->request->post();
        if ($customer->load($post)) {
            $result = $this->updateData($customer, $id);
            MessageUtils::showMessage($result);
            if (!UrlUtils::isEditAction($id)) {
                $customer = new Customer();
            }
        }
        if(!empty($customer->birthday)) {
            $customer->birthday = date('Y') - $customer->birthday;
        }
        return $this->render('update', [
            'model' => $customer,
            'maritalStatus' => MaritalStatusUtil::getDropDownList(false),
            'sex' => SexUtil::getDropdownList(false),
            'channels' => ChanelUtil::getDropdownList(false),
            'backUrl' => '/customer/index',
            'postUrl' => '',
            'categories' => CategoryUtil::getDropDownList(false),
            'jobs' => JobUtil::getDropdownList(false)
        ]);
    }

    private function updateData($model, $id)
    {
        if(!empty($model->birthday)){
            $year = date('Y')-$model->birthday;
            $model->birthday = $year."-01-01";
        }else{
            $model->birthday = null;
        }

        if (UrlUtils::isEditAction($id)) {
            $params = [
                'title',
                'name',
                'phone',
                'email',
                'job',
                'category',
                'salary',
                'birthday',
                'address',
                'marital_status_id',
                'chanel_id',
                'number_of_children',
                'updated_by',
                'updated_at'
            ];
            if(empty($model->birthday)){
               $model->birthday = null;
            }
            $model->updated_by = SessionUtils::getUsername();
            $model->updated_at = date('Y-m-d H:i:s');
            $model->salary = (int)str_replace(',', '', $model->salary);
            return $model->save(true, $params);
        }
        return $model->save();
    }

    public function actionDelete()
    {
        if(PermissionUtil::isHodRole() || PermissionUtil::isAdminRole()) {
            return DeleteDataUtil::delete(new Customer());
        }
        return DeleteDataUtil::updateIsActive(new Customer());
    }

    public function actionExportExcel()
    {
        $temp_folder = "temp";
        $fileUrl = "";
        $post = Yii::$app->request->post();
        if (isset($post)) {
            $khachHang = isset($post['khachHang']) ? $post['khachHang'] : "";
            $nguon = isset($post['nguon']) ? $post['nguon'] : "";
            $userId = isset($post['userId']) ? $post['userId'] : "";

            $objPHPExcel = new PHPExcel();
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel2007");
            $objSheet = $objPHPExcel->getActiveSheet();
            $objPHPExcel->getDefaultStyle()->getFont()->setName('Times New Roman');
            $objPHPExcel->getDefaultStyle()->getFont()->setSize(10);

            $kenh = Chanel::findOne(['id' => $nguon]);
            $nhanVien = UserInfo::findOne(['id' => $userId]);

            // Tieu de bao cao
            $objSheet->setCellValue('A1', "DANH SÁCH KHÁCH HÀNG");
            $objSheet->getRowDimension(1)->setRowHeight(20);
            $objSheet->mergeCells('A1:O1');
            $rangeTitle = $objSheet->getStyle("A1:O1");
            $rangeTitle->getFont()->setBold(true);
            $rangeTitle->getFont()->setSize(18);
            $rangeTitle->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            // Thong tin dieu kien filter danh sach khach hang
            $objSheet->setCellValue('A2', "Từ khóa");
            $objSheet->setCellValue('B2', !empty($khachHang) ? $khachHang : 'Tất cả');
            $objSheet->mergeCells('B2:C2');
            $objSheet->setCellValue('E2', "Kênh");
            $objSheet->setCellValue('F2', !is_null($kenh) ? $kenh->name : 'Tất cả');
            $objSheet->mergeCells('F2:G2');
            $objSheet->setCellValue('A3', "Nhân viên");
            $objSheet->setCellValue('B3', !is_null($nhanVien) ? $nhanVien->name : "Tất cả");
            $objSheet->mergeCells('B3:C3');

            // Cac cot trong danh sach khach hang
            $objSheet->setCellValue('A5', "STT");
            $objSheet->setCellValue('B5', "Tên khách hàng");
            $objSheet->setCellValue('C5', "Giới tính");
            $objSheet->setCellValue('D5', "Tuổi");
            $objSheet->setCellValue('E5', "SĐT");
            $objSheet->setCellValue('F5', "Email");
            $objSheet->setCellValue('G5', "Địa chỉ");
            $objSheet->setCellValue('H5', "Công việc");
            $objSheet->setCellValue('I5', "Thu nhập");
            $objSheet->setCellValue('J5', "Gọi gần nhất");
            $objSheet->setCellValue('K5', "Gặp gần nhất");
            $objSheet->setCellValue('L5', "Phân loại");
            $objSheet->setCellValue('M5', "HĐ");
            $objSheet->setCellValue('N5', "FHC");
            $objSheet->setCellValue('O5', "SIS");
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
            $objSheet->getColumnDimension("M")->setWidth(6);
            $objSheet->getColumnDimension("N")->setWidth(6);
            $objSheet->getColumnDimension("O")->setWidth(6);
            $rangeTitleTable = $objSheet->getStyle("A5:O5");
            $rangeTitleTable->getFont()->setBold(true);
            $rangeTitleTable->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            $model = new CustomerSearch();
            $params = array(
                "CustomerSearch" => array(
                    "name" => $khachHang,
                    "chanel_id" => $nguon,
                    "user_id" => $userId
                )
            );
            $data = $model->search($params, true);

            // binding data
            if ($data->getTotalCount() > 0) {
                $row = 6;
                $stt = 1;
                foreach ($data->getModels() as $index => $model) {
                    $latestCallResult = CallResultUtil::getLatestCallResultByCustomerId($model->id);
                    $latestMeetingResult = MeetingResultUtil::getLatestMeetingResultByCustomerId($model->id);
                    $job = Job::findOne(['id'=>$model->job_id]);

                    $objSheet->setCellValue('A' . $row, $stt++);
                    $objSheet->setCellValue('B' . $row, $model->name);
                    $objSheet->setCellValue('C' . $row, !is_null($model->sex) ? ($model->sex == 1 ? "Nam" : "Nữ") : "");
                    $objSheet->setCellValue('D' . $row, !is_null($model->birthday) ? date_diff(date_create($model->birthday), date_create('now'))->y : "");
                    $objSheet->setCellValue('E' . $row, $model->phone);
                    $objSheet->setCellValue('F' . $row, $model->email);
                    $objSheet->setCellValue('G' . $row, $model->address);
                    $objSheet->setCellValue('H' . $row, !is_null($job) ? $job->name : "");
                    $objSheet->setCellValue('I' . $row, (!empty($model->salary) && $model->salary>0) ? NumberUtils::formatNumberWithDecimal($model->salary, 0) : "");
                    $objSheet->setCellValue('J' . $row, !is_null($latestCallResult) ? (DatetimeUtils::isDatetimeNotEmptyOrNull($latestCallResult->call_date) ? DatetimeUtils::formatDate($latestCallResult->call_date) : "") : "");
                    $objSheet->setCellValue('K' . $row, !is_null($latestMeetingResult) ? (DatetimeUtils::isDatetimeNotEmptyOrNull($latestMeetingResult->meeting_date) ? DatetimeUtils::formatDate($latestMeetingResult->meeting_date) : "") : "");
                    if($model->category ==0){
                        $objSheet->setCellValue('L' . $row, "Cold");
                    }else if($model->category ==1){
                        $objSheet->setCellValue('L' . $row, "Warm");
                    }else if($model->category ==2 ){
                        $objSheet->setCellValue('L' . $row, "Hot");
                    }else{
                        $objSheet->setCellValue('L' . $row, "");
                    }

                    $objSheet->setCellValue('M' . $row, $model->hd);
                    $objSheet->setCellValue('N' . $row, $model->fhc);
                    $objSheet->setCellValue('O' . $row, $model->sis);
                    $row++;
                }
                // bao khung các dòng dữ liệu
                $objSheet->getStyle("A5:O" . ($row - 1))->applyFromArray(
                    array(
                        'borders' => array(
                            'allborders' => array(
                                'style' => \PHPExcel_Style_Border::BORDER_THIN,
                                'color' => array('rgb' => '000000')
                            )
                        )
                    )
                );
                $objSheet->getStyle("A6:O" . $row)->getAlignment()->setWrapText(true);
                $objSheet->getStyle("A6:O" . $row)->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_TOP);
            }

            $objSheet->setTitle('Danh sách khách hàng');

            $objPHPExcel->setActiveSheetIndex(0);
            $fileName = "DSKH_" . SessionUtils::getUsername() . ".xlsx";
            $fileUrl = Url::to([$temp_folder . "/" . $fileName]);
            $objWriter->save($temp_folder . DIRECTORY_SEPARATOR . $fileName);
        }
        return $fileUrl;
    }


    /**
     * Get thông tin khách hàng thông qua ajax
     * @return string
     */
    public function actionGetCustomerInfo()
    {
        $post = Yii::$app->request->post();
        if (isset($post)) {
            $customer_id = isset($post["customer_id"]) ? $post["customer_id"] : "";
            $customer = Customer::findOne(['id' => $customer_id]);
            if (!is_null($customer)) {
                $result = array(
                    "chanel_id" => $customer->chanel_id,
                    "marital_status_id" => $customer->marital_status_id,
                    "job_id" => $customer->job_id
                );
                return json_encode($result);
            }
        }
        return json_encode(array());
    }

    public function actionSaveDelegate()
    {
        if (PermissionUtil::isHodRole() || PermissionUtil::isAdminRole()) {
            $post = Yii::$app->request->post();
            if (isset($post)) {
                $transaction = Yii::$app->db->beginTransaction();
                try {
                    $customers_id = isset($post["customers"]) ? $post["customers"] : array();
                    $staff_id = isset($post["staff_id"]) ? $post["staff_id"] : "";
                    $params = [
                        'user_id',
                        'updated_by',
                        'updated_at'
                    ];
                    if (count($customers_id) > 0 && !empty($staff_id)) {
                        foreach ($customers_id as $index => $item) {
                            $customer = Customer::findOne(['id' => $item]);
                            if (!is_null($customer)) {
                                $customer->user_id = $staff_id;
                                $customer->updated_by = SessionUtils::getUsername();
                                $customer->updated_at = date("Y-m-d H:i:s");
                                $customer->save(true, $params);
                            }
                        }
                        $transaction->commit();
                        return true;
                    }
                } catch (Exception $e) {
                    $transaction->rollBack();
                }
            }
        }
        return false;
    }

    public function actionSetActiveCustomer()
    {
        if (PermissionUtil::isHodRole() || PermissionUtil::isAdminRole()) {
            $post = Yii::$app->request->post();
            if (isset($post)) {
                $transaction = Yii::$app->db->beginTransaction();
                try {
                    $customers_id = isset($post["customers"]) ? $post["customers"] : array();
                    $params = [
                        'is_active',
                        'updated_by',
                        'updated_at'
                    ];
                    if (count($customers_id) > 0) {
                        foreach ($customers_id as $index => $item) {
                            $customer = Customer::findOne(['id' => $item]);
                            if (!is_null($customer)) {
                                $customer->is_active = 1;
                                $customer->updated_by = SessionUtils::getUsername();
                                $customer->updated_at = date("Y-m-d H:i:s");
                                $customer->save(true, $params);
                            }
                        }
                        $transaction->commit();
                        return true;
                    }
                } catch (Exception $e) {
                    $transaction->rollBack();
                }
            }
        }
        return false;
    }

}