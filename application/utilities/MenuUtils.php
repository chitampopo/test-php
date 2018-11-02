<?php
/**
 * Created by PhpStorm.
 * User: phuocnguyen
 * Date: 20/05/2018
 * Time: 2:56 PM
 */

namespace application\utilities;

use Yii;

class MenuUtils
{
    public static function setSelectedMenu($controllers = array())
    {
        $ctl = Yii::$app->controller->id;
        return $ctl == in_array($ctl, $controllers) ? "active" : "";
    }

    public static function setSelectedParentMenu($controller = array())
    {
        $ctl = Yii::$app->controller->id;
        return in_array($ctl, $controller) ? "open" : "";
    }
}