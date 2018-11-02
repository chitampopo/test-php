<?php
/**
 * Created by PhpStorm.
 * User: phuocnguyen
 * Date: 05/09/2018
 * Time: 9:26 PM
 */

namespace application\controllers;
use application\models\Chanel\Chanel;
use application\models\Chanel\ChanelSearch;
use application\utilities\DeleteDataUtil;
use application\utilities\MessageUtils;
use application\utilities\PermissionUtil;
use application\utilities\SessionUtils;
use application\utilities\UrlUtils;
use Yii;
use yii\web\Controller;

class ChanelController extends Controller
{
    public function beforeAction($action)
    {
        PermissionUtil::canAccess('chanel');
        return parent::beforeAction($action);
    }

    public function actionIndex()
    {
        $chanelSearch = new ChanelSearch();
        $params = Yii::$app->request->get();
        $data = $chanelSearch->search($params);
        return $this->render('index', [
            'data' => $data,
            'chanelSearch' => $chanelSearch
        ]);
    }

    public function actionUpdate($id = null)
    {
        $chanel = new Chanel();
        if (UrlUtils::isEditAction($id)) {
            $chanel = Chanel::findOne(['id' => $id]);
        }
        $post = Yii::$app->request->post();
        if ($chanel->load($post)) {
            $result = $this->updateData($chanel, $id);
            if($result){
                MessageUtils::showMessage($result);
            }
            MessageUtils::showMessage($result);
            if (!UrlUtils::isEditAction($id)) {
                $chanel = new Chanel();
            }
        }
        return $this->render('update', ['model' => $chanel]);
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
        return DeleteDataUtil::delete(new Chanel());
    }
}