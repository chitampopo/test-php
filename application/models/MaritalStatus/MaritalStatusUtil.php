<?php
/**
 * Created by PhpStorm.
 * User: Tam
 * Date: 9/7/2018
 * Time: 10:29 PM
 */

namespace application\models\MaritalStatus;


class MaritalStatusUtil
{
    public static function getMaritalStatus()
    {
        return MaritalStatus::find()->orderBy(['name' => SORT_ASC])->all();
    }

    public static function getDropDownList($isRequired = true)
    {
        $results = array();
        if (!$isRequired) {
            $results[''] = '--Chọn--';
        }
        foreach (MaritalStatusUtil::getMaritalStatus() as $index => $postType) {
            $results[$postType->id] = $postType->name;
        }
        return $results;
    }

    public static function getMaritalStatusName($id){
        $obj = MaritalStatus::find()->where(['id' => $id])->one();
        if(!is_null($obj)){
            return $obj->name;
        }
        return "";
    }
}