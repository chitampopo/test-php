<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace application\utilities;

/**
 * Description of NumberUtils
 *
 * @author phuocnguyen
 */
class NumberUtils {
    public static function convertNumberToInt($param) {
        return intval(str_replace(",", "", str_replace(".", "", $param)));
    }
    public static function convertNumberToFloat($param) {        
        return floatval(str_replace(",", ".", str_replace(".", "", $param)));
    }
    public static function formatNumberWithDecimal($number = 0,$numberDecimal=2) {
        return number_format($number, $numberDecimal, ",", ".");
    }
}
