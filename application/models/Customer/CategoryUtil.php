<?php
/**
 * Created by PhpStorm.
 * User: Tam
 * Date: 9/21/2018
 * Time: 11:21 PM
 */

namespace application\models\Customer;
use yii\helpers\ArrayHelper;

class CategoryUtil
{
    public static function getCategories(){
        $models = [
            ['id' => 2, 'name' => 'Hot'],
            ['id' => 1, 'name' => 'Warm'],
            ['id' => 0, 'name' => 'Cold'],
        ];
        return ArrayHelper::map($models, 'id', 'name');
    }

    public static function getDropDownList($isRequired = true){
        $results = CategoryUtil::getCategories();
        if (!$isRequired) {
            $models = [
                ['id' => null, 'name' => '--Chọn--'],
                ['id' => 2, 'name' => 'Hot'],
                ['id' => 1, 'name' => 'Warm'],
                ['id' => 0, 'name' => 'Cold'],
            ];
            return ArrayHelper::map($models, 'id', 'name');
        }

        return $results;
    }
}