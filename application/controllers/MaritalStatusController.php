<?php

namespace application\controllers;
use application\models\MaritalStatus\MaritalStatus;
use application\models\MaritalStatus\MaritalStatusSearch;
use application\utilities\DeleteDataUtil;
use application\utilities\DetectDeviceUtil;
use application\utilities\MessageUtils;
use application\utilities\PermissionUtil;
use application\utilities\SessionUtils;
use application\utilities\UrlUtils;
use yii\web\Controller;
use Yii;

class MaritalStatusController extends Controller
{
    public function beforeAction($action)
    {
        PermissionUtil::canAccess('marital-status');
        return parent::beforeAction($action);
    }
    public function actionIndex()
    {
        $maritalStatusSearch = new MaritalStatusSearch();
        $params = Yii::$app->request->get();
        $data = $maritalStatusSearch->search($params);
        return $this->render('index', [
            'data' => $data,
            'maritalStatusSearch' => $maritalStatusSearch
        ]);
    }

    public function actionUpdate($id = null)
    {
        $maritalStatus = new MaritalStatus();
        if (UrlUtils::isEditAction($id)) {
            $maritalStatus = MaritalStatus::findOne(['id' => $id]);
        }
        $post = Yii::$app->request->post();
        if ($maritalStatus->load($post)) {
            $result = $this->updateData($maritalStatus, $id);
            if($result){
                MessageUtils::showMessage($result);
            }
            MessageUtils::showMessage($result);
            if (!UrlUtils::isEditAction($id)) {
                $maritalStatus = new MaritalStatus();
            }
        }
        return $this->render('update', ['model' => $maritalStatus]);
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
        return DeleteDataUtil::delete(new MaritalStatus());
    }
}
