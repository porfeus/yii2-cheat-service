<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "pages".
 *
 * @property int $id ID
 * @property string $full Полная статья
 * @property string $title Мета Title
 * @property string $meta_key KeyWords
 * @property string $description Description
 * @property string $url URL статьи
 * @property int $date Дата создания
 */
class Pages extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pages';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['full', 'title', 'meta_key', 'description', 'url'], 'required'],
            [['full', 'meta_key', 'description'], 'string'],
            [['date'], 'integer'],
            [['title', 'url'], 'string', 'max' => 256],
            [['url'], 'unique'],
            [['disabled'], 'boolean'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {

            if( $insert ){
              $this->date = time();
            }

            return true;
        }
        return false;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'full' => 'Полная статья',
            'title' => 'Мета Title',
            'meta_key' => 'KeyWords',
            'description' => 'Description',
            'url' => 'URL статьи',
            'date' => 'Дата создания',
        ];
    }
}
