<?php
/**
 * Created by PhpStorm.
 * User: phuocnguyen
 * Date: 11/03/2018
 * Time: 3:18 PM
 */
namespace application\utilities;
use application\models\Department\Department;
use application\models\Level\Level;
use application\Models\User\User;
use Yii;
class SessionUtils
{
    public static function getUsername() {
        return Yii::$app->user->identity->username;
    }
    public static function getUserId() {
        return Yii::$app->user->identity->id;
    }
    public static function getLevel() {
        return Level::findOne(['id'=>Yii::$app->user->identity->level_id]);
    }
    public static function getDepartment() {
        return Department::findOne(['id'=>Yii::$app->user->identity->department_id]);
    }
}