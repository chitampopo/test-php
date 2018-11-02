<?php
/**
 * Created by PhpStorm.
 * User: tam
 * Date: 05/09/2018
 * Time: 7:30 AM
 */
use application\utilities\PagingUtil;
use application\models\Customer\Customer;
use application\utilities\DatetimeUtils;
use application\models\MaritalStatus\MaritalStatusUtil;
use application\models\FhcReport\DemandUtils;
use application\utilities\NumberUtils;
use application\utilities\PermissionUtil;
use application\models\User\User;
?>
<div class="clearfix"></div>
<div class="row">
    <?php
    $models = $data->getModels();
    foreach ($models as $model) {
        $customer = Customer::findOne(['id' => $model->customer_id]);
        if (!is_null($customer)) {
            ?>

            <div class="col-xs-12 col-sm-12 col-md-4 col-lg-3">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <strong><?= $customer->name ?></strong>
                    </div>
                    <table class="table table-bordered">
                        <tr>
                            <td width="80px">SĐT:</td>
                            <td><?= '<a href="tel:"' . $customer->phone . ">" . $customer->phone . "</a>" ?></td>
                        </tr>
                        <tr>
                            <td>Địa chỉ:</td>
                            <td><?= $customer->address ?></td>
                        </tr>
                        <tr>
                            <td>Tuổi:</td>
                            <td> <?php
                            if(DatetimeUtils::isDatetimeNotEmptyOrNull($customer->birthday)) {
                                echo date('Y') - DatetimeUtils::formatDate($customer->birthday, 'Y');
                            } ?></td>
                        </tr>
                        <tr>
                            <td>Hôn nhân:</td>
                            <td> <?= MaritalStatusUtil::getMaritalStatusName($model->marital_status_id) ?></td>
                        </tr>
                        <tr>
                            <td>Số con:</td>
                            <td> <?= $model->number_of_children ?></td>
                        </tr>
                        <tr>
                            <td>Nhu cầu:</td>
                            <td> <?php
                                $result = "";
                                $array = explode(',', $model->demand);
                                foreach ($array as $index => $item) {
                                    $ten = DemandUtils::getName($item);
                                    if (!empty($ten)) {
                                        $result .= $ten . ", ";
                                    }
                                }
                                echo $result;
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Nghề:</td>
                            <td>
                                <?php
                                $job = \application\models\Job\Job::findOne(['id' => $model->job_id]);
                                if (!is_null($job)) {
                                    echo $model->job_id;
                                }
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Mức lương:</td>
                            <td> <?php
                            if(!empty($model->salary)){
                               echo NumberUtils::formatNumberWithDecimal($model->salary, 0);
                            }
                            ?></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>
                                <label><input type="checkbox" disabled <?= $model->sis == 1 ? 'checked' : '' ?>>
                                    SIS</label>&nbsp;&nbsp;
                                <label><input type="checkbox" disabled <?= $model->jfw == 1 ? 'checked' : '' ?>>
                                    JFW</label>&nbsp;&nbsp;
                                <label><strong><?= $model->khtn ?></strong> KHTN</label>&nbsp;&nbsp;
                            </td>
                        </tr>
                        <?php
                        if (!PermissionUtil::isXPRole()) {
                            ?>
                            <tr>
                                <td>Nhân viên</td>
                                <td>
                                    <?php
                                    $user = User::findOne(['id' => $model->user_id]);
                                    if (!is_null($user)) {
                                        echo $user->name;
                                    }
                                    ?>
                                </td>
                            </tr>
                        <?php } ?>
                    </table>
                </div>
            </div>
            <?php
        }
    }?>
</div>
<div class="row">
    <div class="col-xs-4 col-sm-4 col-md-4 text-left"><?= PagingUtil::buildPrevPage() ?></div>
    <div class="col-xs-4 col-sm-4 col-md-4">
        <?=PagingUtil::buildSelectPage($data->getTotalCount(),'fhc-report')?>
    </div>
    <div class="col-xs-4 col-sm-4 col-md-4 text-right"><?= PagingUtil::buildNextPage($data->getTotalCount()) ?></div>
</div>
