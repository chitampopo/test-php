<?php
/**
 * Created by PhpStorm.
 * User: phuocnguyen
 * Date: 06/09/2018
 * Time: 9:10 PM
 */

namespace application\models\Purpose;


class PurposeUtil
{
    public static function getPurposes()
    {
        return Purpose::find()->orderBy(['name' => SORT_ASC])->all();
    }

    public static function getDropdownList($isRequired = true)
    {
        $results = array();
        if (!$isRequired) {
            $results[''] = '--Chọn--';
        }
        foreach (PurposeUtil::getPurposes() as $index => $postType) {
            $results[$postType->id] = $postType->name;
        }
        return $results;
    }
}