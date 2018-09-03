<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "jettarif".
 *
 * @property int $id ID
 * @property string $name Название
 * @property string $count Кол-во кредитов
 * @property double $skidka Скидка
 */
class Jettarif extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'jettarif';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'count', 'skidka'], 'required'],
            [['count'], 'integer'],
            [['skidka'], 'number'],
            [['name'], 'string', 'max' => 1024],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название тарифа',
            'count' => 'Кол-во реалов в пакете',
            'skidka' => 'Скидка для пакета %',
        ];
    }
}
