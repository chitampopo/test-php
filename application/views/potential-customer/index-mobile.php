<?php
/**
 * Created by PhpStorm.
 * User: phuocnguyen
 * Date: 05/09/2018
 * Time: 7:30 AM
 */

use application\utilities\UrlUtils;
use application\utilities\PagingUtil;
use application\models\Customer\Customer;
use application\models\Chanel\Chanel;
use application\utilities\DatetimeUtils;
use application\utilities\PermissionUtil;
use application\models\User\User;
use yii\helpers\Url;
?>
<div class="clearfix"></div>
<div class="row">
    <?php
    $models = $data->getModels();
    foreach ($models as $model) {
        $customer = Customer::findOne(['id' => $model->customer_id]);
        $customer_referral = Customer::findOne(['id' => $model->customer_referral_id]);
        $chanel = Chanel::findOne(['id' => $model->chanel_id]);
        if (!is_null($customer)) {
            ?>
            <div class="col-xs-12 col-sm-12 col-md-4 col-lg-3">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <?php
                        $isEditable = PermissionUtil::userCanNotEditable($model);
                        if ($isEditable) {
                            echo "<strong>" . $customer->name . "</strong>";
                        } else {
                            echo "<strong>" . UrlUtils::buildEditLink('potential-customer', $model->id, $customer->name) . "</strong>";
                        }

                        if (!$isEditable) {
                            ?>
                            <div class="pull-right">
                                <label style="margin-left: 10px;"><a href="javascript:void(0)"
                                                                     onclick="deleteDataMobile('potential-customer','delete','<?= $model->id ?>')"
                                                                     style="color: red"><i class="fa fa-trash-o"></i>
                                        Xóa</a></label>
                            </div>
                        <?php } ?>
                    </div>
                    <table class="table table-bordered">
                        <tr>
                            <td width="80px">SĐT:</td>
                            <td> <?= "<a href='tel:". $customer->phone ."'>". $customer->phone ."</a>" ?></td>
                        </tr>
                        <tr>
                            <td>Giới thiệu:</td>
                            <td> <?= !is_null($customer_referral) ? $customer_referral->name : "" ?></td>
                        </tr>
                        <tr>
                            <td>Nguồn:</td>
                            <td> <?= !is_null($chanel) ? $chanel->name : "" ?></td>
                        </tr>
                        <tr>
                            <td>Ngày gọi:</td>
                            <td> <?= DatetimeUtils::isDatetimeNotEmptyOrNull($model->scheduled_meeting_date) ? DatetimeUtils::formatDate($model->scheduled_meeting_date, "d/m/Y H:i") : "" ?></td>
                        </tr>

                        <?php
                        if (!PermissionUtil::isXPRole()) {
                            $user = User::findOne(['id' => $model->user_id]);
                            ?>
                            <tr>
                                <td>Nhân viên:</td>
                                <td> <?= !is_null($user) ? $user->name : "" ?></td>
                            </tr>
                            <?php
                        }
                        ?>
                        <tr>
                            <td></td>
                            <td>
                                <?php
                                $urlCallResult = Url::to(['/call-result/update?customer=' . $model->customer_id]);
                                $urlMeetingResult =  Url::to(['/meeting-result/update?customer=' . $model->customer_id]);
                                $urlCreateSchedule =  Url::to(['/personal-schedule/update?customer=' . $model->customer_id]);
                                $content = '<div class="btn-group">
                                    <button data-toggle="dropdown" class="btn btn-info btn-sm dropdown-toggle" aria-expanded="true">Hành động<span class="ace-icon fa fa-caret-down icon-on-right"></span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-info dropdown-menu-right">
                                        <li><a href="'.$urlCallResult.'"><i class="fa fa-phone"></i> Tạo KQ cuộc gọi</a></li>
                                        <li><a href="'.$urlMeetingResult.'"><i class="fa fa-handshake-o"></i> Tạo KQ cuộc gặp</a></li>
                                        <li><a href="'.$urlCreateSchedule.'"><i class="fa fa-calendar"></i> Tạo lịch hẹn</a></li>
                                    </ul>
                                </div>';
                                echo $content;
                                 ?>
                            </td>
                        </tr>
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
        <?= PagingUtil::buildSelectPage($data->getTotalCount(), 'potential-customer') ?>
    </div>
    <div class="col-xs-4 col-sm-4 col-md-4 text-right"><?= PagingUtil::buildNextPage($data->getTotalCount()) ?></div>
</div>
