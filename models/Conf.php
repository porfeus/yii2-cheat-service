<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "conf".
 *
 * @property string $id ID
 * @property string $label Описание параметра
 * @property string $name Переменная
 * @property string $value Значение
 */
class Conf extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'conf';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['label', 'name', 'value'], 'required'],
            [['label', 'name', 'value'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'label' => 'Описание параметра',
            'name' => 'Переменная',
            'value' => 'Значение',
        ];
    }



    public static function getParams($name){
        try {
            $value = Conf::find()->where(['name' => $name])->one();
        } catch (\Exception $ex) {
            $value = false;
        }

        if(!$value){
            $value = \Yii::$app->params[$name]??null;
        }else{
            $value = $value->value;
        }

        return $value;
    }

    /**
     * Возвращает адрес настройки
     */
    public static function setIdUrl($id, $rand=0)
    {
      if( !$rand ){
        $rand = rand(111111111,999999999);
      }

      return str_replace([
        '{id}',
        '{random}',
      ],[
        $id,
        $rand,
      ], self::getParams('id-url'));

    }

}
