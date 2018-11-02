<?php


namespace application\validator;
use yii\validators\Validator;
use application\models\Customer\Customer;

class PhoneValidator extends Validator {

    public function validateAttribute($model, $attribute) {
        $value = $model->$attribute;
        if(empty($value)){
            $this->addError($model, $attribute, "Số điện thoại không được để trống");
        }

        if(!isset($model->id)){
            if ($this->isExistPhoneNumber($value)) {
                $this->addError($model, $attribute, "Số điện thoại đã tồn tại");
            }
        } else {
            $oldData = Customer::findUserByPhone($value);
            if(isset($oldData) && $oldData->getAttribute('id') != $model->id){
                $this->addError($model, $attribute, "Số điện thoại đã tồn tại");
            }
        }

    }

    private function isExistPhoneNumber($phone) {
        $exist = Customer::find()->where(['phone' => $phone])->exists();
        return $exist;
    }

}
