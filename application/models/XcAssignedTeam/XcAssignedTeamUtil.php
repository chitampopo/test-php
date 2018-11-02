<?php
/**
 * Created by PhpStorm.
 * User: phuocnguyen
 * Date: 20/10/2018
 * Time: 4:51 PM
 */

namespace application\models\XcAssignedTeam;


use application\utilities\SessionUtils;

class XcAssignedTeamUtil extends XcAssignedTeam
{
    public static function getTeams(){
        return XcAssignedTeam::find()->andWhere(['user_id'=>SessionUtils::getUserId()])->all();
    }

    public static function getTeamIds(){
        $result = array();
        $teams  = XcAssignedTeam::find()->andWhere(['user_id'=>SessionUtils::getUserId()])->all();
        foreach ($teams as $index => $team) {
            $result[] = $team->department_id;
        }
        return $result;
    }
}