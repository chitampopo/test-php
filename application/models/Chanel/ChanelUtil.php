<?php
/**
 * Created by PhpStorm.
 * User: phuocnguyen
 * Date: 06/09/2018
 * Time: 9:03 PM
 */

namespace application\models\Chanel;


class ChanelUtil
{
    public static function getChanels()
    {
        return Chanel::find()->orderBy(['order'=>SORT_ASC,'name' => SORT_ASC])->all();
    }

    public static function getDropdownList($isRequired = true)
    {
        $results = array();
        if (!$isRequired) {
            $results[''] = '--Chọn--';
        }
        foreach (ChanelUtil::getChanels() as $index => $postType) {
            $results[$postType->id] = $postType->name;
        }
        return $results;
    }
}