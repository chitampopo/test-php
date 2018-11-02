<?php
/**
 * Created by PhpStorm.
 * User: phuocnguyen
 * Date: 12/08/2018
 * Time: 6:46 AM
 */

namespace application\controllers;

use application\models\ChangePassword\ChangePassword;
use application\models\User\User;
use application\utilities\DatetimeUtils;
use application\utilities\MessageUtils;
use application\utilities\SessionUtils;
use yii\web\Controller;
use Yii;

class ChangePasswordController extends Controller
{
    public function actionIndex()
    {
        $model = new ChangePassword();
        $post = Yii::$app->request->post();
        if ($model->load($post)) {
            $user = User::findOne(['username'=>SessionUtils::getUsername()]);
            if (Yii::$app->user->login($user)) {
                $user->password_hash = Yii::$app->security->generatePasswordHash($model->newPassword);
                $user->auth_key =  Yii::$app->security->generateRandomKey();
                $user->updated_at = DatetimeUtils::getCurrentDatetime();
                $user->created_by = SessionUtils::getUsername();
                if($user->save()){
                    Yii::$app->user->logout();
                    return $this->goHome();
                }
            } else {
                MessageUtils::showMessage(false, "Mật khẩu hiện tại không đúng");
            }
        }

        return $this->render('index', [
            'model' => $model
        ]);
    }
}