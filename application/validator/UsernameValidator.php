<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace application\validator;
use application\Models\User\User;
use yii\validators\Validator;

class UsernameValidator extends Validator {

    public function validateAttribute($model, $attribute) {
        $value = $model->$attribute;
        $id = $model->id;
        if (!$this->isExistUsername($value, $id)) {
            $this->addError($model, $attribute, "Tên đăng nhập đã tồn tại, Vui lòng chọn tên đăng nhập khác");
        }
    }

    private function isExistUsername($username, $id) {
        $result = false;
        $query = User::find();
        $query->andWhere(["Username" => $username]);
        if (!is_null($id)) {
            $query->andWhere(['<>', "Id", $id]);
        }       
        if ($query->count() == 0) {
            $result = true;
        }
        return $result;
    }

}
