<?php

namespace application\controllers;

use Yii;
use yii\web\Controller;

/**
 * Site controller
 */
class SiteController extends Controller
{
    public function actionIndex()
    {
        $params = array(

        );
        return $this->render('index', $params);
    }

    public function actionError()
    {

        $this->layout = '/login';
        $error = Yii::$app->errorHandler->error;
        if ($error)
            $this->render('error', array('error'=>$error));
        else
            throw new CHttpException(404, 'Page not found.');
    }
    public function actionLogout(){
        Yii::$app->user->logout();
        return $this->goHome();
    }

    public function actionAuthorFailed(){
        return $this->render('author-error');
    }
}
