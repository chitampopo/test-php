<?php
/**
 * Created by PhpStorm.
 * User: phuocnguyen
 * Date: 31/08/2018
 * Time: 8:13 PM
 */

namespace application\utilities;
use Yii;

class DetectDeviceUtil
{
    public static function getDevice()
    {
        return Yii::getAlias('@device') == "tablet" ? "mobile" : Yii::getAlias('@device');
    }

    public static function isDesktop()
    {
        return Yii::getAlias('@device') == 'desktop';
    }

    public static function isMobile()
    {
        return Yii::getAlias('@device') == 'mobile';
    }

    public static function isTablet()
    {
        return Yii::getAlias('@device') == 'tablet';
    }
}