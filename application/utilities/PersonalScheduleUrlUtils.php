<?php

namespace application\utilities;

use application\models\PersonalSchedule\PersonalSchedule;
use Yii;
use yii\helpers\Url;

class PersonalScheduleUrlUtils extends UrlUtils
{
    public static function buildUrl($is_call = true, $disableUrl = false, $id)
    {
        $styleClass = $disableUrl ? "disabled" : "";
        $ctl ="meeting-result";
        if($is_call){
            $ctl ="call-result";
        }
        $rootUrl = UrlUtils::buildPostUrl($ctl);
        if (strpos($rootUrl, "?")) {
            $rootUrl .= "&schedule-id=" . $id;
        } else {
            $rootUrl .= "?go-back=true&ctl=personal-schedule&act=index&schedule-id=" . $id;
        }
        if ($is_call) {
            return "<a href='" . ($disableUrl ? "#" : $rootUrl) . "' class='" . $styleClass . "' title='Click để nhập kết quả gọi'><i class='fa fa-phone'></i> Gọi</a>";
        }
        return "<a href='" . ($disableUrl ? "#" : $rootUrl) . "' class='" . $styleClass . "' title='Click để nhập kết quả hẹn'><i class='fa fa-calendar-check-o'></i> Gặp</a>";
    }

    public static function getPersonalScheduleByDateTime($date, $id)
    {
        return PersonalSchedule::find()
            ->andWhere(['date' => $date])
            ->andWhere(['completed'=>0])
            ->andWhere(['user_id' => SessionUtils::getUserId()])
            ->andWhere("id <> '{$id}'")
            ->all();
    }
}