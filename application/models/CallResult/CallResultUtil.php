<?php

namespace application\models\CallResult;
use application\utilities\DatetimeUtils;
use application\utilities\SessionUtils;
use yii\db\ActiveRecord;

class CallResultUtil extends CallResult
{
    public static function getLatestCallResultByCustomerId($customer_id) 
    {
        return CallResult::find()
            ->where(['customer_id' => $customer_id])
            ->orderBy(['call_date' => SORT_DESC])->one();
    }
}