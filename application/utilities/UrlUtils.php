<?php
/**
 * Created by PhpStorm.
 * User: phuocnguyen
 * Date: 11/03/2018
 * Time: 5:29 PM
 */

namespace application\utilities;

use Yii;
use yii\helpers\Url;

class UrlUtils
{
    public static function getBaseUrl()
    {
        return Yii::getAlias('@web');
    }

    public static function buildEditLink($controller, $id, $title, $action = '', $params = array())
    {
        if (empty($action)) {
            $action = 'update';
        }
        $url = "/{$controller}/{$action}?id={$id}";
        if (!empty($params)) {
            foreach ($params as $key => $value) {
                $url .= "&{$key}={$value}";
            }
        }
        return "<a href='" . Url::to([$url]) . "' title='Click để xem hoặc chỉnh sửa thông tin'>{$title}</a>";
    }

    public static function isEditAction($id)
    {
        return !is_null($id) && !empty($id);
    }

    public static function isGoBack()
    {
        $get = Yii::$app->request->get();
        return isset($get['go-back']);
    }

    public static function buildPostUrl($postCtl, $postAction = 'update')
    {
        $get = Yii::$app->request->get();
        if (UrlUtils::isGoBack()) {
            $ctl = isset($get['ctl']) ? $get['ctl'] : Yii::$app->controller->id;
            $action = isset($get['action']) ? $get['action'] : "index";
            $id = isset($get['id']) ? $get['id'] : "";
            $url = "?go-back=true&ctl=" . $ctl . "&action=" . $action;
            if (!empty($id)) {
                $url .= "&id=" . $id;
            }

            return Url::to(["/{$postCtl}/{$postAction}".$url]);
        }
        return Url::to(["/{$postCtl}/{$postAction}"]);
    }

    public static function buildGoBackUrl($resultId ='')
    {
        $get = Yii::$app->request->get();
        if (UrlUtils::isGoBack()) {
            $ctl = isset($get['ctl']) ? $get['ctl'] : Yii::$app->controller->id;
            $action = isset($get['action']) ? $get['action'] : "index";
            $id = isset($get['id']) ? $get['id'] : "";
            $url = "/{$ctl}/{$action}";
            $url.="?go-back=true";
            if (!empty($id)) {
                $url .= "&id={$id}";
            }
            if(!empty($resultId)){
                $url .= "&result-id={$resultId}";
            }
            return Url::to($url);
        }
        return "";
    }

    public static function isUrlExist($url)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($code == 200) {
            $status = true;
        } else {
            $status = false;
        }
        curl_close($ch);
        return $status;
    }
}