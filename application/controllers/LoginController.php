<?php
/**
 * Created by PhpStorm.
 * User: phuocnguyen
 * Date: 05/04/2018
 * Time: 9:02 PM
 */

namespace application\controllers;

use application\models\User\LoginForm;
use application\models\User\User;
use yii\web\Controller;
use Yii;
use yii\helpers\Url;

class LoginController extends Controller
{

    public function actionIndex()
    {
        $model = new LoginForm();
        $model->load(Yii::$app->request->post());
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            $this->goBack();

        } else {
            $this->layout = '/login';
            return $this->render('index', [
                'model' => $model
            ]);
        }

    }

    public function actionLogout()
    {
        Yii::$app->session->removeAll();
        $this->redirect(Url::to(['/login']));
    }

}