<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "materials".
 *
 * @property int $id
 * @property string $title
 * @property string $short
 * @property string $full
 * @property string $meta_key
 * @property string $description
 * @property string $url
 * @property int $date
 */
class Materials extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'materials';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'short', 'full', 'meta_key', 'description', 'url'], 'required'],
            [['short', 'full', 'meta_key', 'description'], 'string'],
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
            'short' => 'Короткая статья',
            'full' => 'Полная статья',
            'title' => 'Мета Title',
            'meta_key' => 'KeyWords',
            'description' => 'Description',
            'url' => 'Url статьи',
            'date' => 'Дата создания',
        ];
    }
}
