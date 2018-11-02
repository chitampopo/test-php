<?php
/**
 * Created by PhpStorm.
 * User: phuocnguyen
 * Date: 11/03/2018
 * Time: 3:20 PM
 */

namespace application\utilities;


class DatetimeUtils
{
    public static function getCurrentDate($format = 'Y-m-d')
    {
        return date($format);
    }

    public static function getCurrentDatetime($format = 'Y-m-d H:i:s')
    {
        return date($format);
    }

    public static function formatDate($date, $format = 'd/m/Y')
    {
        return date($format, strtotime($date));
    }

    public static function convertStringToDate($str)
    {
        $result = null;
        if(DetectDeviceUtil::isMobile()){
            return $str;
        }
        if (!is_null($str) && !empty($str)) {
            $arrDate = explode('/', $str);
            if (count($arrDate) > 0) {
                $day = isset($arrDate[0]) ? $arrDate[0] : 01;
                $month = isset($arrDate[1]) ? $arrDate[1] : 01;
                $year = isset($arrDate[2]) ? $arrDate[2] : 1900;
                $result = $year . "-" . $month . "-" . $day;
            }
        }
        return $result;
    }

    public static function convertStringToDateTime($str, $hour, $minute)
    {
        $result = null;
        if(DetectDeviceUtil::isMobile()){
            return $str." {$hour}:{$minute}:00";
        }
        if (!is_null($str) && !empty($str)) {
            $arrDate = explode('/', $str);
            if (count($arrDate) > 0) {
                $day = isset($arrDate[0]) ? $arrDate[0] : 01;
                $month = isset($arrDate[1]) ? $arrDate[1] : 01;
                $year = isset($arrDate[2]) ? $arrDate[2] : 1900;
                $result = $year . "-" . $month . "-" . $day . " {$hour}:{$minute}:00";
            }
        }
        return $result;
    }

    public static function isNotEmptyOrNull($date)
    {
        if (!is_null($date)) {
            if (!empty($date)) {
                if ($date !== '0000-00-00') {
                    return true;
                }
            }
        }
        return false;
    }

    public static function isDatetimeNotEmptyOrNull($date)
    {
        if (!is_null($date)) {
            if (!empty($date)) {
                if ($date !== '0000-00-00 00:00:00') {
                    return true;
                }
            }
        }
        return false;
    }

    public static function buildInputHour($modalName, $data='')
    {
        $result = "<select class='form-control input-sm' name='{$modalName}[hour]'>";
        for ($i = 0; $i <= 23; $i++) {
            if($data==$i){
                $result.="<option value='{$i}' selected>{$i} giờ</option>";
            }else{
                $result.="<option value='{$i}'>{$i} giờ</option>";
            }
        }
        $result .= "</select>";
        return $result;
    }

    public static function buildInputMinute($modalName, $data='')
    {
        $result = "<select class='form-control input-sm' name='{$modalName}[minute]'>";
        for ($i = 0; $i <= 59; $i=$i+5) {
            if($data==$i){
                $result.="<option value='{$i}' selected>{$i} phút</option>";
            }else{
                $result.="<option value='{$i}'>{$i} phút</option>";
            }
        }
        $result .= "</select>";
        return $result;
    }

    public static function getCurrentDateDependOnDevice(){
        if(DetectDeviceUtil::isMobile()){
            return date('Y-m-d');
        }
        return date('d/m/Y');
    }

    public static function getFirstDayOfMonthDateDependOnDevice(){
        if(DetectDeviceUtil::isMobile()){
            return date('Y-m-01');
        }
        return date('01/m/Y');
    }
}