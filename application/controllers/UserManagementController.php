<?php

namespace application\controllers;

use application\models\User\User;
use application\models\User\UserInfo;
use application\models\User\UserInfoSearch;
use application\models\Level\LevelUtil;
use application\models\Department\DepartmentUtil;
use application\models\User\UserUtil;
use application\utilities\DeleteDataUtil;
use application\utilities\DetectDeviceUtil;
use application\utilities\DatetimeUtils;
use application\utilities\MessageUtils;
use application\utilities\PermissionUtil;
use application\utilities\SessionUtils;
use application\utilities\UrlUtils;
use yii\web\Controller;
use Yii;

class UserManagementController extends Controller
{
    public function beforeAction($action)
    {
        PermissionUtil::canAccess('user-management');
        return parent::beforeAction($action);
    }

    public function actionIndex()
    {
        $userInfoSearch = new UserInfoSearch();
        $params = Yii::$app->request->get();
        $data = $userInfoSearch->search($params);
        return $this->render('index', [
            'data' => $data,
            'userInfoSearch' => $userInfoSearch,
            'levels' => LevelUtil::getDropdownList(false),
            'departments' => DepartmentUtil::getDropdownList(false)
        ]);
    }

    public function actionUpdate($id = null)
    {
        $userInfo = new UserInfo();
        if (UrlUtils::isEditAction($id)) {
            $userInfo = UserInfo::findOne(['id' => $id]);
        }
        $post = Yii::$app->request->post();
        if ($userInfo->load($post)) {
            $result = $this->updateData($userInfo, $id);
            if ($result) {
                MessageUtils::showMessage($result);
            }
            MessageUtils::showMessage($result);
            if (!UrlUtils::isEditAction($id)) {
                $userInfo = new UserInfo();
            }
        }
        return $this->render('update', [
            'model' => $userInfo,
            'levels' => LevelUtil::getDropdownList(false),
            'departments' => DepartmentUtil::getDropdownListForUserManagement(false)
        ]);
    }

    private function updateData($model, $id)
    {
        if (UrlUtils::isEditAction($id)) {
            $params = [
                'name',
                'username',
                'phone',
                'email',
                'address',
                'level_id',
                'department_id',
                'is_active',
                'updated_by',
                'updated_at'
            ];

            $model->updated_by = SessionUtils::getUsername();
            $model->updated_at = date('Y-m-d H:i:s');
            return $model->save(true, $params);
        }
        $generatedPassword = Yii::$app->security->generateRandomString(6);
        $model->password_hash = Yii::$app->security->generatePasswordHash($generatedPassword);
        $model->auth_key = Yii::$app->security->generateRandomKey();
        $model->updated_at = DatetimeUtils::getCurrentDatetime();
        $model->updated_by = SessionUtils::getUsername();
        $saved = $model->save();
        if ($saved) {
            $mail = "Tài khoản đăng nhập: {$model->username}<br>Mật khẩu: {$generatedPassword}";
            Yii::$app->mail->compose('layouts/html', ['content' => $mail])
                ->setFrom(Yii::$app->params['appEmail'])
                ->setTo($model->email)
                ->setSubject('[AIA CRM] TÀI KHOẢN MỚI')
                ->send();
        }
        return $saved;
    }

    public function actionDelete()
    {
        return DeleteDataUtil::delete(new UserInfo());
    }

    public function actionResetPassword()
    {
        $post = Yii::$app->request->post();
        if (isset($post)) {
            if (isset($post['userId'])) {
                $id = $post['userId'];
                $user = UserInfo::findOne(['id' => $id]);
                $generatedPassword = Yii::$app->security->generateRandomString(6);
                $user->password_hash = Yii::$app->security->generatePasswordHash($generatedPassword);
                $user->auth_key = Yii::$app->security->generateRandomKey();
                $user->updated_at = DatetimeUtils::getCurrentDatetime();
                $user->updated_by = SessionUtils::getUsername();
                if ($user->save()) {
                    $mail = "Tài khoản đăng nhập: {$user->username}<br>Mật khẩu: {$generatedPassword}";
                    Yii::$app->mail->compose('layouts/html', ['content' => $mail])
                        ->setFrom(Yii::$app->params['appEmail'])
                        ->setTo($user->email)
                        ->setSubject('[AIA CRM] LẤY LẠI MẬT KHẨU')
                        ->send();
                    return "success";
                }
            }
        }
    }

    /**
     * Get list nhân viên theo phòng khi thực hiện change phòng trên view thông qua ajax
     * @return string
     */
    public function actionGetNhanViens()
    {
        $post = Yii::$app->request->post();
        $result = "";
        if (isset($post)) {
            $department_id = isset($post["department_id"]) ? $post["department_id"] : "";
            $is_load_leader = isset($post["is_load_leader"]) ? $post["is_load_leader"] : 0;
            $isNotRequired = isset($post["is_not_required"]) ? $post["is_not_required"] : 0;
            if ($isNotRequired==1) {
                $result .= "<option value=''>--Chọn--</option>";
            }
            $users = User::find()->andWhere(['is_active' => 1])->andWhere("id<>1");
            if ($is_load_leader == 0) {
                $users->andWhere(['level_id' => 1]);
            }
            if(!empty($department_id)){
                $users->andWhere(['department_id' => $department_id]);
            }else if(PermissionUtil::isXPMRole()) {
                $users->andWhere(['department_id' => $department_id]);
            }else if(PermissionUtil::isXCRole()){
                $users->andWhere(['id' => UserUtil::getUserIdByXcRole()]);
            }else {
                if (!empty($department_id)) {
                    $users->andWhere(['department_id' => $department_id]);
                }
            }
            foreach ($users->all() as $index => $user) {
                $result .= "<option value='{$user->id}'>{$user->name}</option>";
            }
        }
        return $result;
    }
}