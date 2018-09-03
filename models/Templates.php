<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "templates".
 *
 * @property int $id ID
 * @property string $name Название
 * @property string $value Содержимое
 * @property int $disabled Отключен
 */
class Templates extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'templates';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'value'], 'required'],
            [['name'], 'string', 'max' => 256],
            [['name'], 'unique'],
            [['value'], 'string'],
            [['disabled'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название',
            'value' => 'Содержимое',
            'disabled' => 'Отключен',
        ];
    }
}
