<?php
/**
 * Created by PhpStorm.
 * User: phuocnguyen
 * Date: 29/09/2018
 * Time: 3:11 PM
 */

namespace application\controllers;
use application\models\Job\Job;
use application\models\Job\JobSearch;
use application\utilities\DeleteDataUtil;
use application\utilities\MessageUtils;
use application\utilities\PermissionUtil;
use application\utilities\SessionUtils;
use application\utilities\UrlUtils;
use yii\web\Controller;
use Yii;
class JobController extends Controller
{
    public function beforeAction($action)
    {
        PermissionUtil::canAccess('chanel');
        return parent::beforeAction($action);
    }

    public function actionIndex()
    {
        $jobSearch = new JobSearch();
        $params = Yii::$app->request->get();
        $data = $jobSearch->search($params);
        return $this->render('index', [
            'data' => $data,
            'jobSearch' => $jobSearch
        ]);
    }

    public function actionUpdate($id = null)
    {
        $job = new Job();
        if (UrlUtils::isEditAction($id)) {
            $job = Job::findOne(['id' => $id]);
        }
        $post = Yii::$app->request->post();
        if ($job->load($post)) {
            $result = $this->updateData($job, $id);
            if($result){
                MessageUtils::showMessage($result);
            }
            MessageUtils::showMessage($result);
            if (!UrlUtils::isEditAction($id)) {
                $job = new Job();
            }
        }
        return $this->render('update', ['model' => $job]);
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
        return DeleteDataUtil::delete(new Job());
    }
}