<?php

namespace application\controllers;
use application\models\Department\Department;
use application\models\Department\DepartmentSearch;
use application\utilities\DeleteDataUtil;
use application\utilities\MessageUtils;
use application\utilities\PermissionUtil;
use application\utilities\SessionUtils;
use application\utilities\UrlUtils;
use yii\web\Controller;
use Yii;

class DepartmentController extends Controller
{
    public function beforeAction($action)
    {
        PermissionUtil::canAccess('department');
        return parent::beforeAction($action);
    }
    public function actionIndex()
    {
        $departmentSearch = new DepartmentSearch();
        $params = Yii::$app->request->get();
        $data = $departmentSearch->search($params);
        return $this->render('index', [
            'data' => $data,
            'departmentSearch' => $departmentSearch
        ]);
    }

    public function actionUpdate($id = null)
    {
        $department = new Department();
        if (UrlUtils::isEditAction($id)) {
            $department = Department::findOne(['id' => $id]);
        }
        $post = Yii::$app->request->post();
        if ($department->load($post)) {
            $result = $this->updateData($department, $id);
            if($result){
                MessageUtils::showMessage($result);
            }
            MessageUtils::showMessage($result);
            if (!UrlUtils::isEditAction($id)) {
                $department = new Department();
            }
        }
        return $this->render('update', ['model' => $department]);
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
        return DeleteDataUtil::delete(new Department());
    }
}