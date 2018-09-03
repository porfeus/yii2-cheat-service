<?php

namespace app\models;

use Yii;

class ApiRequests extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'api_requests';
    }
}
