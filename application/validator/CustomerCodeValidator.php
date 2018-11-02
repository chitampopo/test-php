<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace application\Validator;
use application\Models\KhachHang\KhachHang;
use yii\validators\Validator;

class CustomerCodeValidator extends Validator {

    public function validateAttribute($model, $attribute) {
        $value = $model->$attribute;
        $id = $model->Id;
        if (!$this->isExistCustomerCode($value, $id)) {
            $this->addError($model, $attribute, "Mã khách hàng đã có, Vui lòng nhập mã khách hàng khác");
        }
    }

    private function isExistCustomerCode($username, $id) {
        $result = false;
        $query = KhachHang::find();
        $query->andWhere(["Code" => $username]);
        if (!is_null($id)) {
            $query->andWhere(['<>', "Id", $id]);
        }       
        if ($query->count() == 0) {
            $result = true;
        }
        return $result;
    }

}
