<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "time_limits_users".
 *
 * @property int $id
 * @property int $user_id
 * @property int $limit_id
 * @property int $time время (в секундах)
 */
class TimeLimitsUsers extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'time_limits_users';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'limit_id', 'time'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'limit_id' => 'Limit ID',
            'time' => 'время (в секундах)',
        ];
    }
}
