<?php

namespace application\models\Department;


use application\models\XcAssignedTeam\XcAssignedTeam;
use application\models\XcAssignedTeam\XcAssignedTeamUtil;
use application\utilities\PermissionUtil;
use application\utilities\SessionUtils;

class DepartmentUtil
{
    public static function getDepartments()
    {
        $query = Department::find()
            ->andWhere("id <> 10")
            ->orderBy(['name' => SORT_ASC]);
        if (PermissionUtil::isXCRole()) {
            $teams = XcAssignedTeamUtil::getTeamIds();
            $query->andWhere(['id' => $teams]);
        }
        return $query->all();

    }
    public static function getDropdownListForUserManagement($isRequired = true)
    {
        $results = array();
        if (!$isRequired) {
            $results[''] = '--Chọn--';
        }
        $departments = Department::find()->all();

        foreach ($departments as $index => $dept) {
            $results[$dept->id] = $dept->name;
        }
        return $results;
    }
    public static function getDropdownList($isRequired = true)
    {
        $results = array();
        if (!$isRequired) {
            $results[''] = '--Chọn--';
        }
        $departments = DepartmentUtil::getDepartments();
        if (PermissionUtil::isXPMRole()) {
            $departments = Department::find()->andWhere(['id' => SessionUtils::getDepartment()->id])->all();
        }
        foreach ($departments as $index => $dept) {
            $results[$dept->id] = $dept->name;
        }
        return $results;
    }

    public static function getDropdownListByDepartments($departments, $isRequired = true)
    {
        $results = array();
        if (!$isRequired) {
            $results[''] = '--Chọn--';
        }
        foreach ($departments as $index => $dept) {
            $results[$dept->id] = $dept->name;
        }
        return $results;
    }

    public static function getDepartmentsIsNotAssigned()
    {
        return Department::find()
            ->join('left join', 'xc_assigned_team', 'department_id=department.id')
            ->where(['is', 'xc_assigned_team.department_id', null])
            ->all();
    }

}
