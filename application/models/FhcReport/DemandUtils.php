<?php
/**
 * Created by PhpStorm.
 * User: Tam
 * Date: 9/14/2018
 * Time: 8:37 PM
 */

namespace application\models\FhcReport;


use yii\helpers\ArrayHelper;

class DemandUtils
{

    public static function getDemands(){
        $models = [
            ['id' => 1, 'name' => 'Học vấn'],
            ['id' => 2, 'name' => 'Hưu trí'],
            ['id' => 3, 'name' => 'Sức khỏe'],
            ['id' => 4, 'name' => 'Bảo vệ'],
            ['id' => 5, 'name' => 'Đầu tư'],
        ];
        return ArrayHelper::map($models, 'id', 'name');
    }

    public static function getDropDownList($isRequired = true){
        $results = DemandUtils::getDemands();
        if (!$isRequired) {
            $models = [
                ['id' => null, 'name' => '--Chọn--'],
                ['id' => 1, 'name' => 'Học vấn'],
                ['id' => 2, 'name' => 'Hưu trí'],
                ['id' => 3, 'name' => 'Sức khỏe'],
                ['id' => 4, 'name' => 'Bảo vệ'],
                ['id' => 5, 'name' => 'Đầu tư'],
            ];
            return ArrayHelper::map($models, 'id', 'name');
        }

        return $results;
    }

    public static function getName($id){
        $demands = [
            ['id' => 1, 'name' => 'Học vấn'],
            ['id' => 2, 'name' => 'Hưu trí'],
            ['id' => 3, 'name' => 'Sức khỏe'],
            ['id' => 4, 'name' => 'Bảo vệ'],
            ['id' => 5, 'name' => 'Đầu tư'],
        ];
        foreach ($demands as $demand){
            if(strcmp($demand['id'], $id) == 0 ){
                return $demand['name'];
            }
        }
        return '';
    }

}
