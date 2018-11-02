<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace application\Validator;
use application\Models\User\User;
use application\Utilities\SessionUtils;
use yii\validators\Validator;

class ChangePasswordValidator extends Validator {

    public function validateAttribute($model, $attribute) {
        echo var_dump($model);die;
        $value = $model->$attribute;
        if ($this->isExistUser($value)) {
            $this->addError($model, $attribute, "Tên đăng nhập đã tồn tại, Vui lòng chọn tên đăng nhập khác");
        }
    }

    private function isExistUser($password) {
        $result = false;
        $query = User::find();
        $query->andWhere(["Username" => SessionUtils::getUsername()]);
        $query->andWhere(['Password'=>md5($password)]);
        if ($query->count() == 1) {
            $result = true;
        }
        return $result;
    }

}
