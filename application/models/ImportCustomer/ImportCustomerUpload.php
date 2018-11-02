<?php
/**
 * Created by PhpStorm.
 * User: phuocnguyen
 * Date: 14/10/2018
 * Time: 3:37 PM
 */
namespace application\models\ImportCustomer;

class ImportCustomerUpload extends \yii\base\Model
{
    public $filedinhkem;

    public function rules() {
        return [
            ['filedinhkem', 'trim'],
            ['filedinhkem', 'required','message' => 'Bạn phải chọn file excel'],
            [['filedinhkem'], 'file', 'extensions' => 'xlsx'],
        ];
    }

    public function attributeLabels() {
        return [
            'filedinhkem' => 'File excel'
        ];
    }
}