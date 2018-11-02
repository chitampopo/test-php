<?php
/**
 * Created by PhpStorm.
 * User: phuocnguyen
 * Date: 30/06/2018
 * Time: 10:09 AM
 */

namespace application\models\User;
use yii\db\ActiveRecord;
use application\utilities\SessionUtils;
use application\utilities\DatetimeUtils;
use yii\helpers\ArrayHelper;
use yii\web\IdentityInterface;
use Yii;
class User extends ActiveRecord implements IdentityInterface
{
    private static $_instance = null;
    public static function getInstance()
    {
        if (null === static::$_instance) {
            static::$_instance = new static();
        }
        return static::$_instance;
    }

    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(),[]);
    }
    public static function tableName()
    {
        return '{{user}}';
    }

    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['access_token' => $token]);
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAuthKey()
    {
        return $this->auth_key;
    }

    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }
    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'is_active' => 1]);
    }
    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    public function rules()
    {
        $rule = [
            ['username', 'trim'],
            ['name', 'trim'],
            ['phone', 'trim'],
            ['password_hash', 'trim'],
            ['email', 'trim'],
            ['address', 'trim'],
            ['auth_key', 'trim'],
            ['last_login_date', 'trim'],
            ['is_active','trim'],
            ['department_id','trim'],
            ['level_id','trim'],

        ];
        return $rule;
    }

    public function attributeLabels()
    {
        return [

        ];
    }
}