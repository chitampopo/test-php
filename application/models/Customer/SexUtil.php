<?php
/**
 * Created by PhpStorm.
 * User: Tam
 * Date: 9/9/2018
 * Time: 11:25 AM
 */

namespace application\models\Customer;
use yii\helpers\ArrayHelper;

class SexUtil {

    public static function getSex(){
        $models = [
            ['id' => 1, 'name' => 'Nam'],
            ['id' => 0, 'name' => 'Nữ'],
        ];
        return ArrayHelper::map($models,'id','name');
    }

    public static function getDropdownList($isRequired = true){
        $results = SexUtil::getSex();
        if (!$isRequired) {
            $models = [
                ['id' => null, 'name' => '--Chọn--'],
                ['id' => 1, 'name' => 'Nam'],
                ['id' => 0, 'name' => 'Nữ'],
            ];
            return ArrayHelper::map($models,'id','name');
        }

        return $results;
    }
}