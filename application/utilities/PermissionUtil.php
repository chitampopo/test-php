<?php
/**
 * Created by PhpStorm.
 * User: phuocnguyen
 * Date: 06/09/2018
 * Time: 6:43 AM
 */

namespace application\utilities;

use Yii;
use yii\helpers\Url;
use yii\web\ForbiddenHttpException;

class PermissionUtil
{
    public static function isXPRole()
    {
        return Yii::$app->user->identity->level_id == 1;
    }

    public static function isXPMRole()
    {
        return Yii::$app->user->identity->level_id == 2;
    }

    public static function isXCRole()
    {
        return Yii::$app->user->identity->level_id == 5;
    }

    public static function isHodRole()
    {
        return Yii::$app->user->identity->level_id == 3;
    }

    public static function isAdminRole()
    {
        return Yii::$app->user->identity->level_id == 4;
    }

    public static function showCheckboxInListRalatedPermission($model)
    {
        return ["value" => $model->id, 'disabled' => PermissionUtil::userCanNotEditable($model)];
    }

    public static function userCanNotEditable($model)
    {
        if (PermissionUtil::isAdminRole() || PermissionUtil::isHodRole()) {
            return false;
        }
        return  $model->user_id != SessionUtils::getUserId();
    }

    public static function canAccess($controller)
    {
        if(Yii::$app->request->isAjax){
            return true;
        }
        $function_for_xp_role = array(
            'xc-assigned-team','call-result', 'meeting-result', 'fhc-report', 'potential-customer', 'customer', 'daily-review', 'personal-schedule'
        );
        $function_for_xpm_role = array(
            'call-result', 'meeting-result', 'fhc-report', 'potential-customer', 'customer', 'daily-review', 'personal-schedule', 'department-shedule', 'jfw-schedule', 'call-statistics-and-potential-customer'
        );
        $function_for_hod_role = array(
            'call-result', 'meeting-result', 'fhc-report', 'potential-customer', 'customer', 'daily-review', 'personal-schedule', 'department-shedule', 'jfw-schedule', 'call-statistics-and-potential-customer', 'sis-analysis'
        );

        if (PermissionUtil::isXPRole() && !in_array($controller, $function_for_xp_role)) {
            return Yii::$app->getResponse()->redirect(Url::to(['/site/author-failed']));
        }

        if (PermissionUtil::isXPMRole() && !in_array($controller, $function_for_xpm_role)) {
            return Yii::$app->getResponse()->redirect(Url::to(['/site/author-failed']));
        }

        if (PermissionUtil::isHodRole() && !in_array($controller, $function_for_hod_role)) {
            return Yii::$app->getResponse()->redirect(Url::to(['/site/author-failed']));
        }
        if(PermissionUtil::isAdminRole()){
            return true;
        }
        
    }
}