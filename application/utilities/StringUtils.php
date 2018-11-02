<?php
/**
 * Created by PhpStorm.
 * User: phuocnguyen
 * Date: 11/03/2018
 * Time: 3:33 PM
 */

namespace application\utilities;


class StringUtils
{
    public static function isEmpty($str)
    {
        return is_null($str) && empty($str);
    }

    public static function stringValueOf($object, $field, $valueType='string')
    {
        $defaultValue = "";
        if($valueType=="int"){
            $defaultValue = 0;
        }
        if ($object == null) {
            return $defaultValue;
        }
        if (!isset($object[$field])) {
            return $defaultValue;
        }
        return $object[$field];
    }

    public static function convert2Alias($str = '') {
        $vMap = array(
            'é' => 'e',
            'è' => 'e',
            'ẻ' => 'e',
            'ẽ' => 'e',
            'ẹ' => 'e',
            'ý' => 'y',
            'ỳ' => 'y',
            'ỷ' => 'y',
            'ỹ' => 'y',
            'ỵ' => 'y',
            'ú' => 'u',
            'ù' => 'u',
            'ủ' => 'u',
            'ũ' => 'u',
            'ụ' => 'u',
            'ư' => 'u',
            'ứ' => 'u',
            'ừ' => 'u',
            'ử' => 'u',
            'ữ' => 'u',
            'ự' => 'u',
            'í' => 'i',
            'ì' => 'i',
            'ỉ' => 'i',
            'ĩ' => 'i',
            'ị' => 'i',
            'ó' => 'o',
            'ò' => 'o',
            'ỏ' => 'o',
            'õ' => 'o',
            'ọ' => 'o',
            'ô' => 'o',
            'ố' => 'o',
            'ồ' => 'o',
            'ổ' => 'o',
            'ỗ' => 'o',
            'ộ' => 'o',
            'á' => 'a',
            'à' => 'a',
            'ả' => 'a',
            'ã' => 'a',
            'ạ' => 'a',
            'â' => 'a',
            'ấ' => 'a',
            'ầ' => 'a',
            'ẩ' => 'a',
            'ẫ' => 'a',
            'ậ' => 'a',
            'ă' => 'a',
            'ắ' => 'a',
            'ằ' => 'a',
            'ẳ' => 'a',
            'ẵ' => 'a',
            'ặ' => 'a',
            'ê' => 'e',
            'ế' => 'e',
            'ể' => 'e',
            'ễ' => 'e',
            'ệ' => 'e',
            'ơ' => 'o',
            'ớ' => 'o',
            'ờ' => 'o',
            'ở' => 'o',
            'ỡ' => 'o',
            'ợ' => 'o',
            'É' => 'e',
            'È' => 'e',
            'Ẻ' => 'e',
            'Ẽ' => 'e',
            'Ẹ' => 'e',
            'Ê' => 'e',
            'Ế' => 'e',
            'Ề' => 'e',
            'Ể' => 'e',
            'Ễ' => 'e',
            'Ệ' => 'e',
            'Ý' => 'y',
            'Ỳ' => 'y',
            'Ỷ' => 'y',
            'Ỹ' => 'y',
            'Ỵ' => 'y',
            'Ú' => 'u',
            'Ù' => 'u',
            'Ủ' => 'u',
            'Ũ' => 'u',
            'Ụ' => 'u',
            'Ư' => 'u',
            'Ứ' => 'u',
            'Ừ' => 'u',
            'Ử' => 'u',
            'Ữ' => 'u',
            'Ự' => 'u',
            'Í' => 'i',
            'Ì' => 'i',
            'Ỉ' => 'i',
            'Ĩ' => 'i',
            'Ị' => 'i',
            'Ó' => 'o',
            'Ò' => 'o',
            'Ỏ' => 'o',
            'Õ' => 'o',
            'Ọ' => 'o',
            'Ô' => 'o',
            'Ố' => 'o',
            'Ồ' => 'o',
            'Ổ' => 'o',
            'Ỗ' => 'o',
            'Ộ' => 'o',
            'Ơ' => 'o',
            'Ớ' => 'o',
            'Ờ' => 'o',
            'Ở' => 'o',
            'Ỡ' => 'o',
            'Ợ' => 'o',
            'Á' => 'a',
            'À' => 'a',
            'Ả' => 'a',
            'Ã' => 'a',
            'Ạ' => 'a',
            'Â' => 'a',
            'Ấ' => 'a',
            'Ầ' => 'a',
            'Ẩ' => 'a',
            'Ẫ' => 'a',
            'Ậ' => 'a',
            'Ă' => 'a',
            'Ắ' => 'a',
            'Ằ' => 'a',
            'Ẳ' => 'a',
            'Ẵ' => 'a',
            'Ặ' => 'a',
            'đ' => 'd',
            'Đ' => 'd',
        );
        $str = strtolower(preg_replace('/[^0-9a-zA-Z\_\-]/', '-', strtr(strtr($str, $vMap), array(' ' => '-'))));
        $ii = 0;
        $new = '';
        while ($ii < strlen($str)) {
            if ($str[$ii] == '-') {
                $new = $new . $str[$ii];
                $ii++;
                while ($str[$ii] == '-' && $ii < strlen($str)) {
                    $ii++;
                }
            } else {
                $new = $new . $str[$ii];
                $ii++;
            }
        }
        return $new;
    }
}