<?php
/**
 * Created by PhpStorm.
 * User: phuocnguyen
 * Date: 13/07/2018
 * Time: 7:14 PM
 */

namespace application;
use application\models\user\User;
use Yii;
use yii\web\ForbiddenHttpException;
class WebUser extends \yii\web\User
{
    private $_access = [];

    public function loginRequired($checkAjax = true, $checkAcceptHeader = true)
    {
        $request = Yii::$app->getRequest();

        if ($this->enableSession && (!$checkAjax || !$request->getIsAjax())) {
            $this->setReturnUrl($request->getUrl());
        }
        if ($this->loginUrl !== null) {
            $loginUrl = (array) $this->loginUrl;
            if ($loginUrl[0] !== Yii::$app->requestedRoute) {
                return Yii::$app->getResponse()->redirect([$this->loginUrl[0]]);
            }
        }
        throw new ForbiddenHttpException(Yii::t('yii', 'Login Required'));
    }

    public function getIsAdmin()
    {
        if (($identity = $this->getIdentity()) === null) {
            return false;
        }
        if ($identity->group_id == User::GROUP_ADMIN) {
            return true;
        }
        if ($this->can('admin')) {
            return true;
        }
        return false;
    }

    public function getAsAdmin()
    {
        if ($this->getIsAdmin() && Yii::$app->session->has('loginIsAdmin')) {
            return true;
        }
        return false;
    }

    public function getGroupId()
    {
        if (($identity = $this->getIdentity()) === null) {
            return false;
        }
        return $identity->group_id;
    }

    /**
     * Checks if the User can perform the operation as specified by the given permission.
     *
     * Note that you must configure "authManager" application component in order to use this method.
     * Otherwise it will always return false.
     *
     * @param string $permissionName the name of the permission (e.g. "edit post") that needs access check.
     * @param array $params name-value pairs that would be passed to the rules associated
     * with the roles and permissions assigned to the User.
     * @param boolean $allowCaching whether to allow caching the result of access check.
     * When this parameter is true (default), if the access check of an operation was performed
     * before, its result will be directly returned when calling this method to check the same
     * operation. If this parameter is false, this method will always call
     * [[\yii\rbac\ManagerInterface::checkAccess()]] to obtain the up-to-date access result. Note that this
     * caching is effective only within the same request and only works when `$params = []`.
     * @return boolean whether the User can perform the operation as specified by the given permission.
     */
    public function can($permissionName, $params = [], $allowCaching = true)
    {
        if (($userCan = DmDonViAuth::getInstance()->can($permissionName)) == true) {
            return true;
        }

        if ($allowCaching && empty($params) && isset($this->_access[$permissionName])) {
            return $this->_access[$permissionName];
        }

        if (($manager = $this->getAccessChecker()) === null) {
            return false;
        }

        if ($this->getGroupId() == User::GROUP_ADMIN) {
            return true;
        }

        $access = $manager->checkAccess($this->getId(), $permissionName, $params);
        if ($allowCaching && empty($params)) {
            $this->_access[$permissionName] = $access;
        }

        return $access;
    }
}