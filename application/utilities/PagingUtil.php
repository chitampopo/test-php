<?php
/**
 * Created by PhpStorm.
 * User: phuocnguyen
 * Date: 05/09/2018
 * Time: 7:43 PM
 */

namespace application\utilities;


use yii\helpers\Url;

class PagingUtil
{
    public static function buildSelectPage($totalRow, $controller)
    {
        $rowPerPage = \Yii::$app->params['page_size'];
        $numberOfPage = ceil($totalRow / $rowPerPage);
        $result = "";
        $currentPage = PagingUtil::getCurrentPage();
        if ($numberOfPage > 1) {
            $result .= "<select class='form-control input-sm' id='{$controller}-paging' onchange=paging('{$controller}')>";
            for ($i = 1; $i <= $numberOfPage; $i++) {
                if ($currentPage == $i) {
                    $result .= "<option value='{$i}' selected>Trang {$i}</option>";
                } else {
                    $result .= "<option value='{$i}' >Trang {$i}</option>";
                }
            }
            $result .= "</select>";
        }
        return $result;
    }

    public static function buildNextPage($totalRow)
    {
        $rowPerPage = \Yii::$app->params['page_size'];
        $numberOfPage = ceil($totalRow / $rowPerPage);
        $currentPage = PagingUtil::getCurrentPage();
        if($currentPage>=$numberOfPage){
            return "";
        }
        $nextPage = $currentPage + 1;
        $url = Url::to(['/level/index?page=' . $nextPage."&per-page=".\Yii::$app->params['page_size']]);
        return "<a href='{$url}'><i class='fa fa-arrow-circle-right' aria-hidden='true'></i> Trang kế</a>";
    }

    public static function buildPrevPage()
    {
        $currentPage = PagingUtil::getCurrentPage();
        $prevPage = $currentPage - 1;
        if ($prevPage < 0) {
            $prevPage = 0;
        }
        if($prevPage==0){
            return "";
        }
        $url = Url::to(['/level/index?page=' . $prevPage."&per-page=".\Yii::$app->params['page_size']]);
        return "<a href='{$url}'><i class='fa fa-arrow-circle-left' aria-hidden='true'></i> Trang trước</a>";
    }

    private static function getCurrentPage()
    {
        $get = \Yii::$app->request->get();
        return isset($get['page']) ? $get['page'] : 1;
    }
}