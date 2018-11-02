<?php
namespace application\utilities;
use Yii;
class MessageUtils {
    public static function showMessage($result, $message = '') {
        if ($result) {
            Yii::$app->session->setFlash('success', empty($message) ? 'Cập nhật dữ liệu thành công' : $message);
            return true;
        } else {
            Yii::$app->session->setFlash('error', empty($message) ? 'Không thể cập nhật dữ liệu' : $message);
            return false;
        }
    }

}
