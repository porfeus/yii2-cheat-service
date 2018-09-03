<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "templatesconf".
 *
 * @property int $id ID
 * @property int $name Название
 * @property int $value Описание
 * @property int $url Адрес
 */
class Templatesconf extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'templatesconf';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
          [['name', 'value', 'url'], 'required'],
          [['name'], 'string', 'max' => 256],
          [['value'], 'string'],
          [['url'], 'unique'],
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
            'value' => 'Описание',
            'url' => 'Адрес',
        ];
    }
}
