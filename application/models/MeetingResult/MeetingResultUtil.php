<?php

namespace application\models\MeetingResult;
use application\utilities\DatetimeUtils;
use application\utilities\SessionUtils;
use yii\db\ActiveRecord;

class MeetingResultUtil extends MeetingResult
{
    public static function getLatestMeetingResultByCustomerId($customer_id) 
    {
        return MeetingResult::find()
            ->where(['customer_id' => $customer_id])
            ->orderBy(['meeting_date' => SORT_DESC])->one();
    }
}