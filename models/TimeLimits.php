<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "time_limits".
 *
 * @property int $id
 * @property string $type тип запроса
 * @property string $description описание запроса
 * @property int $default время по-умолчанию (в секундах)
 */
class TimeLimits extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'time_limits';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['default'], 'integer'],
            [['type'], 'string', 'max' => 50],
            [['description'], 'string', 'max' => 250],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => 'тип запроса',
            'description' => 'описание запроса',
            'default' => 'время по-умолчанию (в секундах)',
        ];
    }
}
