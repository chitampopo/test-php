<?php
/**
 * Created by PhpStorm.
 * User: phuocnguyen
 * Date: 11/07/2018
 * Time: 8:24 PM
 */

namespace application;

use Yii;
use yii\web\ForbiddenHttpException;
class AccessControl extends \yii\filters\AccessControl
{
    /**
     * @var array List of action that not need to check access.
     */
    public $ignoreActions = [];

    public $ignoreGroups = [];

    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {

        $user = Yii::$app->user;
        if($action->controller->id == "login"){
            return true;
        }

        if(!$user->isGuest){
            if($action->controller->id == "site"){
                return true;
            }
            return true;
        }
        $this->denyAccess($user);
    }

    /**
     * Returns a value indicating whether the filer is active for the given action.
     * @param \yii\base\Action $action the action being filtered
     * @return boolean whether the filer is active for the given action.
     */
    protected function isActive($action)
    {
        if ($this->owner instanceof Module) {
            // convert action uniqueId into an ID relative to the module
            $mid = $this->owner->getUniqueId();
            $id = $action->getUniqueId();
            if ($mid !== '' && strpos($id, $mid) === 0) {
                $id = substr($id, strlen($mid) + 1);
            }
        } else {
            $id = $action->id;
        }

        foreach ($this->ignoreActions as $route) {
            if (substr($route, -1) === '*') {
                $route = rtrim($route, '*');
                if ($route === '' || strpos($id, $route) === 0) {
                    return false;
                }
            } else {
                if ($id === $route) {
                    return false;
                }
            }
        }

        return !in_array($id, $this->except, true) && (empty($this->only) || in_array($id, $this->only, true));
    }

    protected function denyAccess($user)
    {
        if ($user->getIsGuest()) {
            $user->loginRequired();
        } else {
            throw new ForbiddenHttpException('You are not allowed to perform this action.');
        }
    }
}