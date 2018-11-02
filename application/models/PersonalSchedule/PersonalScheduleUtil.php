<?php

namespace application\models\PersonalSchedule;


use application\utilities\PermissionUtil;
use application\utilities\SessionUtils;

class PersonalScheduleUtil extends PersonalSchedule
{
    public static function getScheduleCompletedDropdownList($isRequired = true)
    {
        $results = array();
        if (!$isRequired) {
            $results[''] = '--Chọn--';
        }
        $results['1'] = 'Đã hoàn thành';
        $results['0'] = 'Chưa hoàn thành';
        return $results;
    }

    public static function getScheduleByDate($date_from, $date_to)
    {
        return PersonalSchedule::find()
            ->andWhere(['user_id' => SessionUtils::getUserId()])
            ->andWhere(['completed' => 0])
            ->andWhere("date between '{$date_from}' and '{$date_to}'")
            ->all();

    }

}