<?php

namespace application\models\Customer;
use application\utilities\DatetimeUtils;
use application\utilities\SessionUtils;
use application\validator\PhoneValidator;
use yii\db\ActiveRecord;
use Yii;
class Customer extends ActiveRecord
{
    public static function tableName()
    {
        return '{{customer}}';
    }

    public function actionCreate()
    {
        $model = new Customer();
        
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        }
        
        return $this->render('index', ['model' => $model]);
    }

    public function rules()
    {
        return [
            ['name', 'required', 'message' => 'Tên khách hàng không được để trống'],
            ['email', 'email'],
            ['phone', 'required'],
            [['sex', 'category', 'salary','is_lock_change_category','disabled','birthday'],  'trim'],
            ['phone', PhoneValidator::className()],
            [['title', 'job_id', 'address', 'marital_status_id'], 'string'],
            [['sex',  'number_of_children', 'chanel_id'], 'integer'],
            ['is_lock_change_category', 'default', 'value' => 0],
            ['disabled', 'default', 'value' => 0],
            ['user_id', 'default', 'value' => SessionUtils::getUserId()],
            ['created_at', 'default', 'value' => DatetimeUtils::getCurrentDatetime()],
            ['updated_at', 'default', 'value' => DatetimeUtils::getCurrentDatetime()],
            ['created_by', 'default', 'value' => SessionUtils::getUsername()],
            ['updated_by', 'default', 'value' => SessionUtils::getUsername()]

        ];
    }

    public static function findUserByPhone($phone){
        return Customer::find()->where(['phone' => $phone])->one();
    }

}