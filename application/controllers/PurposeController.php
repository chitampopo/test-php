<?php
/**
 * Created by PhpStorm.
 * User: phuocnguyen
 * Date: 05/09/2018
 * Time: 10:08 PM
 */

namespace application\controllers;


use application\models\Purpose\Purpose;
use application\models\Purpose\PurposeSearch;
use application\utilities\DeleteDataUtil;
use application\utilities\MessageUtils;
use application\utilities\PermissionUtil;
use application\utilities\SessionUtils;
use application\utilities\UrlUtils;
use yii\web\Controller;
use Yii;
class PurposeController extends Controller
{
    public function beforeAction($action)
    {
        PermissionUtil::canAccess('purpose');
        return parent::beforeAction($action);
    }
    public function actionIndex()
    {
        $purposeSearch = new PurposeSearch();
        $params = Yii::$app->request->get();
        $data = $purposeSearch->search($params);
        return $this->render('index', [
            'data' => $data,
            'purposeSearch' => $purposeSearch
        ]);
    }

    public function actionUpdate($id = null)
    {
        $purpose = new Purpose();
        if (UrlUtils::isEditAction($id)) {
            $purpose = Purpose::findOne(['id' => $id]);
        }
        $post = Yii::$app->request->post();
        if ($purpose->load($post)) {
            $result = $this->updateData($purpose, $id);
            if($result){
                MessageUtils::showMessage($result);
            }
            MessageUtils::showMessage($result);
            if (!UrlUtils::isEditAction($id)) {
                $purpose = new Purpose();
            }
        }
        return $this->render('update', ['model' => $purpose]);
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
        return DeleteDataUtil::delete(new Purpose());
    }
}