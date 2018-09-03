<?php

namespace app\models;

use Yii;
use yii\base\Model;

class ChangeSettingsForm extends OrderForm
{
  static $tiketTitle = [
    'mail_title' => 'Заявка на изменение настройки',
    'mail_message' => 'изменение настройки',
  ];

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $parentRules = parent::rules();
        foreach($parentRules as $i=>$rule){
          if( !in_array($rule[0][0], array('title', 'filesList', 'message')) ){
            unset($parentRules[$i]);
          }
        }

        return array_merge([
          [['message'], 'string', 'max' => 2000, 'tooLong'=>'Сообщение должно содержать максимум 2000 символов.'],
        ], $parentRules);
    }
}
