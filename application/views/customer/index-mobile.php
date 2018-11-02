<?php
/**
 * Created by PhpStorm.
 * User: tam
 * Date: 05/09/2018
 * Time: 7:30 AM
 */
use application\utilities\UrlUtils;
use application\utilities\PagingUtil;
use application\models\Chanel\Chanel;
use application\models\CallResult\CallResultUtil;
use application\models\MeetingResult\MeetingResultUtil;
use application\utilities\DatetimeUtils;
use application\utilities\PermissionUtil;
use yii\helpers\Url;
?>
<div class="clearfix"></div>
<div class="row">
    <?php
    $models = $data->getModels();
    $stt = 1;
    foreach ($models as $model) {
        $customer = \application\models\Customer\Customer::findOne(['id'=>$model->id])?>

        <div class="col-xs-12 col-sm-12 col-md-4 col-lg-3">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <?php
                    $isEditable = PermissionUtil::userCanNotEditable($model);
                    if ($isEditable) {
                        echo "<strong>".$stt.". ".$customer->name."</strong>";
                    }else{
                        echo "<strong>".$stt.". ".UrlUtils::buildEditLink('customer', $model->id, $customer->name)."</strong>";
                    }
                    $stt++;
                    $chanel = Chanel::findOne(['id' => $model->chanel_id]);
                    $latestCallResult = CallResultUtil::getLatestCallResultByCustomerId($model->id);
                    $latestMeetingResult = MeetingResultUtil::getLatestMeetingResultByCustomerId($model->id);
                    if(!$isEditable){
                    ?>
                    <div class="pull-right">
                        <label style="margin-left: 10px;"><a href="javascript:void(0)" onclick="deleteDataMobile('customer','delete','<?=$model->id?>')" style="color: red"><i class="fa fa-trash-o"></i> Xóa</a> </label>
                    </div>
                    <?php } ?>
                </div>
                <table class="table table-bordered">
                    <tr>
                        <td width="80px">Xưng hô:</td>
                        <td> <?= $customer->title ?></td>
                    </tr>
                    <tr>
                        <td width="80px">Họ tên:</td>
                        <td> <?= $customer->name ?></td>
                    </tr>
                    <tr>
                        <td width="80px">SĐT:</td>
                        <td>  <?= "<a href='tel:". $customer->phone ."'>". $customer->phone ."</a>" ?></td>
                    </tr>
                    <tr>
                        <td width="80px">Email:</td>
                        <td>  <?= "<a href='mailto:". $customer->email ."'>". $customer->email ."</a>" ?></td>
                    </tr>
                    <tr>
                        <td width="80px">Gọi gần nhất:</td>
                        <td>  <?= !is_null($latestCallResult) ? (DatetimeUtils::isDatetimeNotEmptyOrNull($latestCallResult->call_date) ? DatetimeUtils::formatDate($latestCallResult->call_date):"") : ""; ?></td>
                    </tr>
                    <tr>
                        <td width="80px">Gặp gần nhất:</td>
                        <td>  <?= !is_null($latestMeetingResult) ? (DatetimeUtils::isDatetimeNotEmptyOrNull($latestMeetingResult->meeting_date) ? DatetimeUtils::formatDate($latestMeetingResult->meeting_date):"") : ""; ?></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>
                            <?php
                            $urlCallResult = Url::to(['/call-result/update?customer=' . $model->id]);
                            $urlMeetingResult =  Url::to(['/meeting-result/update?customer=' . $model->id]);
                            $urlCreateSchedule =  Url::to(['/personal-schedule/update?customer=' . $model->id]);
                            echo '<div class="btn-group">
        <button data-toggle="dropdown" class="btn btn-info btn-sm dropdown-toggle" aria-expanded="true">Hành động<span class="ace-icon fa fa-caret-down icon-on-right"></span>
												</button>
												<ul class="dropdown-menu dropdown-info dropdown-menu-right">
													<li><a href="'.$urlCallResult.'"><i class="fa fa-phone"></i> Tạo KQ cuộc gọi</a></li>
													<li><a href="'.$urlMeetingResult.'"><i class="fa fa-handshake-o"></i> Tạo KQ cuộc gặp</a></li>
													<li><a href="'.$urlCreateSchedule.'"><i class="fa fa-calendar"></i> Tạo lịch hẹn</a></li>
												</ul>
											</div>';
                            ?>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        <?php
    } ?>
</div>
<div class="row">
    <div class="col-xs-4 col-sm-4 col-md-4 text-left"><?= PagingUtil::buildPrevPage() ?></div>
    <div class="col-xs-4 col-sm-4 col-md-4">
        <?=PagingUtil::buildSelectPage($data->getTotalCount(),'customer')?>
    </div>
    <div class="col-xs-4 col-sm-4 col-md-4 text-right"><?= PagingUtil::buildNextPage($data->getTotalCount()) ?></div>
</div>
