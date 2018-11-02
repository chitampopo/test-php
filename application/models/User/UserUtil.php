<?php
/**
 * Created by PhpStorm.
 * User: phuocnguyen
 * Date: 20/07/2018
 * Time: 12:18 PM
 */

namespace application\models\User;


use application\models\Department\DepartmentUtil;
use application\utilities\PermissionUtil;
use application\utilities\SessionUtils;

class UserUtil extends User
{
    public static function getUsers()
    {
        $query = User::find()->orderBy(['name' => SORT_ASC]);
        if (PermissionUtil::isXPMRole()) {
            $query->andWhere(['department_id' => SessionUtils::getDepartment()->id]);
        }
        if (PermissionUtil::isXCRole()) {
            $departments = DepartmentUtil::getDepartments();
            $ids = array();
            foreach ($departments as $index => $department) {
                $ids[] = $department->id;
            }
            $users = User::find()->andWhere(['department_id' => $ids])->all();
            $userids = array();
            foreach ($users as $index => $user) {
                $userids[] = $user->id;
            }
            $query->andWhere(['id' => $userids]);
        }
        return $query->all();
    }

    public static function getDropdownList($isRequired = true)
    {
        $results = array();
        if (!$isRequired) {
            $results[''] = '--Chọn--';
        }
        foreach (UserUtil::getUsers() as $index => $obj) {
            $results[$obj->id] = $obj->name;
        }
        return $results;
    }

    public static function getDropdownListByUsers($users, $isRequired = true)
    {
        $results = array();
        if (!$isRequired) {
            $results[''] = '--Chọn--';
        }
        foreach ($users as $index => $obj) {
            $results[$obj->id] = $obj->name;
        }
        return $results;
    }

    public static function getDropdownListRelatedToUsers($isRequired = true, $list)
    {
        $results = array();
        if (!$isRequired) {
            $results[''] = '--Chọn--';
        }
        foreach ($list as $index => $obj) {
            $results[$obj->id] = $obj->name;
        }
        return $results;
    }

    public static function getUserByDepartment($department)
    {
        $query = User::find()->orderBy(['name' => SORT_ASC]);
        $query->andWhere(['department_id' => $department]);
        return $query->all();
    }

    public static function getUserIdByDepartment()
    {
        $result = array();
        foreach (UserUtil::getUsers() as $index => $user) {
            $result[] = $user->id;
        }
        return $result;
    }

    public static function getXpmUsers()
    {
        return User::find()->andWhere(['is_active' => 1])
            ->andWhere(['level_id' => 2])
            ->all();
    }

    public static function getXcUsers()
    {
        return User::find()->andWhere(['is_active' => 1])
            ->andWhere(['level_id' => 5])
            ->all();
    }

    public static function getUserIdByXcRole(){
        $result = array();
        foreach (UserUtil::getUsers() as $index => $user) {
            $result[]=$user->id;
        }
        return $result;
    }
}