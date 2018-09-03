<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "cron_links".
 *
 * @property int $id
 * @property string $name
 * @property string $link
 */
class CronLinks extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'cron_links';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'link'], 'required'],
            [['name', 'link'], 'string', 'max' => 256],
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
            'link' => 'Ссылка',
        ];
    }

    public function getHtmlLink(){
      return '<a href="'.$this->link.'" target="_blank" class="blank">'.$this->name.'</a>';
    }

    public function getHtmlLinkWithoutName(){
      return '<a href="'.$this->link.'" target="_blank" class="blank">'.$this->link.'</a>';
    }
}
