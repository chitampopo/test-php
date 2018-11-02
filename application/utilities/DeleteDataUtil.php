<?php
/**
 * Created by PhpStorm.
 * User: phuocnguyen
 * Date: 05/09/2018
 * Time: 7:11 AM
 */

namespace application\utilities;

use application\models\Customer\Customer;
use Yii;

class DeleteDataUtil
{
    public static function delete($object)
    {
        $post = Yii::$app->request->post();
        $values = null;
        if (isset($post)) {
            $values = isset($post["values"]) ? $post["values"] : null;
        }
        if (!is_null($values)) {
            $where = array('id' => $values);
            $result = $object::deleteAll($where);
            if ($result > 0) {
                return 1;
            } else {
                return 0;
            }
        } else {
            return -1;
        }
    }

    public static function updateIsActive($object)
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $post = Yii::$app->request->post();
            $values = null;
            if (isset($post)) {
                $values = isset($post["values"]) ? $post["values"] : null;
            }
            if (!is_null($values)) {
                $params = [
                    'is_active',
                    'updated_by',
                    'updated_at'
                ];
                foreach ($values as $index => $value) {
                    $_object = $object::findOne(['id' => $value]);
                    if (!is_null($_object)) {
                        $_object->is_active = 0;
                        $_object->updated_at = date('Y-m-d H:i:s');
                        $_object->updated_by = SessionUtils::getUsername();
                        $_object->save(true, $params);
                    }
                }
                $transaction->commit();
                return 1;
            } else {
                return -1;
            }
        } catch (Exception $e) {
            $transaction->rollBack();
        }
        return 0;
    }
}