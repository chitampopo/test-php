<?php
/**
 * Created by PhpStorm.
 * User: phuocnguyen
 * Date: 29/09/2018
 * Time: 10:11 AM
 */

namespace application\models\Job;


class JobUtil extends Job
{
    public static function getJobs()
    {
        return Job::find()->orderBy(['name' => SORT_ASC])->all();
    }

    public static function getDropdownList($isRequired = true)
    {
        $results = array();
        if (!$isRequired) {
            $results[''] = '--Chọn--';
        }
        foreach (JobUtil::getJobs() as $index => $level) {
            $results[$level->id] = $level->name;
        }
        return $results;
    }
}