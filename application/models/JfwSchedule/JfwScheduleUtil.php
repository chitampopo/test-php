<?php

namespace application\models\JfwSchedule;


use application\utilities\PermissionUtil;
use application\utilities\SessionUtils;

class JfwScheduleUtil extends JfwSchedule
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

}