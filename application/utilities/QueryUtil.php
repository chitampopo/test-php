<?php
/**
 * Created by PhpStorm.
 * User: phuocnguyen
 * Date: 30/09/2018
 * Time: 8:30 PM
 */

namespace application\utilities;


class QueryUtil
{
    public static function getQuerySelectUserIdInDepartment($department_id){
        return "select id from user where department_id = '{$department_id}'";
    }

    public static function getQuerySelectUserIdInTeamAssigned($user_id){
        return "SELECT  user.id FROM 
                team_assigned JOIN department ON team_assigned.department_id = department.id
                JOIN `user` ON department.id = user.department_id
                WHERE team_assigned.user_id='{$user_id}'";
    }
}