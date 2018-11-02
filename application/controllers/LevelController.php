<?php
/**
 * Created by PhpStorm.
 * User: phuocnguyen
 * Date: 04/09/2018
 * Time: 11:35 PM
 */

namespace application\controllers;


use application\models\Level\Level;
use application\models\Level\LevelSearch;
use application\utilities\DeleteDataUtil;
use application\utilities\DetectDeviceUtil;
use application\utilities\MessageUtils;
use application\utilities\PermissionUtil;
use application\utilities\SessionUtils;
use application\utilities\UrlUtils;
use yii\helpers\Url;
use yii\web\Controller;
use Yii;

class LevelController extends Controller
{
    public function beforeAction($action)
    {
        PermissionUtil::canAccess('level');
        return parent::beforeAction($action);
    }

    public function actionIndex()
    {
        $levelSearch = new LevelSearch();
        $params = Yii::$app->request->get();
        $data = $levelSearch->search($params);
        return $this->render('index', [
            'data' => $data,
            'levelSearch' => $levelSearch
        ]);
    }

    public function actionUpdate($id = null)
    {
        $level = new Level();
        if (UrlUtils::isEditAction($id)) {
            $level = Level::findOne(['id' => $id]);
        }
        $post = Yii::$app->request->post();
        if ($level->load($post)) {
            $result = $this->updateData($level, $id);
            if($result){
                MessageUtils::showMessage($result);
            }
            MessageUtils::showMessage($result);
            if (!UrlUtils::isEditAction($id)) {
                $level = new Level();
            }
        }
        return $this->render('update', ['model' => $level]);
    }

    private function updateData($model, $id)
    {
        if (UrlUtils::isEditAction($id)) {
            $params = [
                'name',
                'description',
                'updated_by',
                'updated_at'
            ];

            $model->updated_by = SessionUtils::getUsername();
            $model->updated_at = date('Y-m-d H:i:s');
            return $model->save(true, $params);
        }
        return $model->save();
    }

    public function actionDelete()
    {
        return DeleteDataUtil::delete(new Level());
    }
}