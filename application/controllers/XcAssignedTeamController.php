<?php

namespace application\controllers;

use application\models\User\UserUtil;
use application\models\Department\DepartmentUtil;
use application\models\XcAssignedTeam\XcAssignedTeam;
use application\utilities\MessageUtils;
use application\utilities\PermissionUtil;
use application\utilities\UrlUtils;
use yii\web\Controller;
use Yii;

class XcAssignedTeamController extends Controller
{
    public function beforeAction($action)
    {
        PermissionUtil::canAccess('xc-assigned-team');
        return parent::beforeAction($action);
    }

    public function actionIndex($id = null)
    {
        $xcAssignedTeam = new XcAssignedTeam();
        $post = Yii::$app->request->post();
        if (!is_null($id)) {
            $xcAssignedTeam->user_id = $id;
            $teams = array();
            foreach (XcAssignedTeam::find()->andWhere(['user_id' => $id])->all() as $index => $team) {
                $teams[] = $team->department_id;
            }
            $xcAssignedTeam->department_id = $teams;
        }
        if ($xcAssignedTeam->load($post)) {
            $result = $this->saveData($xcAssignedTeam);
            MessageUtils::showMessage($result);
        }
        return $this->render('update', [
            'model' => $xcAssignedTeam,
            'users' => UserUtil::getDropdownListByUsers(UserUtil::getXcUsers(), false),
            'departments' => DepartmentUtil::getDropdownList(false)
        ]);
    }

    private function saveData($xcAssignedTeam)
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (!empty($xcAssignedTeam->department_id)) {
                $where = array('user_id' => $xcAssignedTeam->user_id);
                XcAssignedTeam::deleteAll($where);
                foreach ($xcAssignedTeam->department_id as $index => $item) {
                    $model = new XcAssignedTeam();
                    $model->user_id = $xcAssignedTeam->user_id;
                    $model->department_id = $item;
                    $model->save();
                }
            }
            $transaction->commit();
            return true;
            return true;
        } catch (Exception $e) {
            $transaction->rollBack();
        }
        return false;
    }
}