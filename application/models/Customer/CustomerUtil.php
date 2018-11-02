<?php
/**
 * Created by PhpStorm.
 * User: phuocnguyen
 * Date: 06/09/2018
 * Time: 10:06 PM
 */

namespace application\models\Customer;


use application\models\User\UserUtil;
use application\utilities\PermissionUtil;
use application\utilities\QueryUtil;
use application\utilities\SessionUtils;

class CustomerUtil
{
    public static function getCustomers()
    {
        return CustomerUtil::buildQueryGetCustomers()->all();
    }

    public static function buildQueryGetCustomers()
    {
        $query = Customer::find()->orderBy(['name' => SORT_ASC]);
        if (!(PermissionUtil::isAdminRole() || PermissionUtil::isHodRole())) {
            $query->andWhere(['is_active' => 1]);
        }
        if (PermissionUtil::isXPRole()) {
            $query->andWhere(['user_id' => SessionUtils::getUserId()]);
            $query->andWhere(['disabled' => 0]);
        } else if (PermissionUtil::isXPMRole()) {
            $department_id = SessionUtils::getDepartment()->id;
            $sql = QueryUtil::getQuerySelectUserIdInDepartment($department_id);
            $query->andWhere(" user_id in ({$sql})");
            $query->andWhere(['disabled' => 0]);
        } else if (PermissionUtil::isXCRole()) {
            $users = UserUtil::getUserIdByXcRole();
            $query->where(['in', 'user_id' ,$users]);
        }
        return $query;
    }

    public static function getDropdownList($isRequired = true)
    {
        $results = array();
        if (!$isRequired) {
            $results[''] = '--Chọn--';
        }
        foreach (CustomerUtil::getCustomers() as $index => $obj) {
            $results[$obj->id] = $obj->name . ' - ' . $obj->phone;
        }
        return $results;
    }

    public static function getDropDownListOfUser($isRequired = true, $id = 0)
    {
        $results = array();
        if (!$isRequired) {
            $results[''] = '--Chọn--';
        }
        foreach (CustomerUtil::getCustomers() as $index => $obj) {
            if ($id != 0 && $obj->user_id == $id) {
                $results[$obj->id] = $obj->name . ' - ' . $obj->phone;
            }
        }
        return $results;
    }

    public static function getCustomerNameByID($id)
    {
        return Customer::find()->where(['id' => $id])->one()->name;
    }

    public static function getCustomerByID($id)
    {
        return Customer::find()->where(['id' => $id])->one();
    }

    public static function isDuplicatePhonenumber($phone){
        return Customer::find()
            ->andWhere(['phone'=>$phone])
            ->count() >0;

    }
}