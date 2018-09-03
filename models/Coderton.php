<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "coderton".
 *
 * @property int $id
 * @property string $command
 * @property int $pair Парный?
 * @property string $code_before Код до
 * @property string $code_after Код после
 * @property string $defaults Значения по умолчанию
 */
class Coderton extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'coderton';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['command', 'pair', 'code_before'], 'required'],
            [['pair'], 'integer'],
            [['code_before', 'code_after'], 'string'],
            [['command', 'defaults'], 'string', 'max' => 256],
            [['command'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'command' => 'Команда',
            'pair' => 'Парность',
            'code_before' => 'Код до',
            'code_after' => 'Код после',
            'defaults' => 'Значения по умолчанию',
        ];
    }

    /**
     * Устанавливает значения по умолчанию, если не задано иное
     */
    public static function setDefaultValues(&$code)
    {
        //получаем список тегов с параметрами
        preg_match_all('@\{v:([^:]+):([0-9]+)\}@', $code, $matches);
        if( empty($matches[0]) ) return;

        foreach($matches[0] as $i=>$template){
          $command = $matches[1][$i];

          //получаем инфо о текущей команде
          $model = self::find()->where(['command' => $command])->one();

          //получаем список параметров
          $values = explode(',', $model->defaults);
          foreach($values as $a=>$value){
            $n = $a + 1;
            //заменяем параметры на значения по умолчанию
            $code = str_replace('{v:'.$command.':'.$n.'}', $value, $code);
          }
        }
    }

    /**
     * Помечаем шаблоны ключами для дальнейшей обработки: {v1} = {v:realclick:1}
     */
    public static function setTemplatesKeys(&$model)
    {
        //если не заданы параметры по умолчанию
        if( empty($model->defaults) ) return;

        //получаем параметры по умолчанию
        $values = explode(',', $model->defaults);
        foreach($values as $i=>$value){
          $n = $i + 1;
          //заменяем в коде до
          $model->code_before = str_replace(
            '{v'.$n.'}',
            '{v:'.$model->command.':'.$n.'}',
            $model->code_before
          );
          //заменяем в коде после
          $model->code_after = str_replace(
            '{v'.$n.'}',
            '{v:'.$model->command.':'.$n.'}',
            $model->code_after
          );
        }
    }

    /**
     * @inheritdoc
     */
    public static function setParserData(&$tags, &$placeholders, &$vals, &$a, &$b)
    {
        //Массив, описывающий состав и начертание открывающих и закрывающих тэгов
        $tags = [];

        //массив ЧТО будем заменять
        $placeholders = [];

        //перечисляем теги
        $vals = [];

        //парные теги {}текст{/}
        $a = [];

        //не парные - одиночные теги {}
        $b = [];

        //получаем список всех кодов
        $models = self::find()->all();
        $tagNum = 0;

        foreach($models as $i=>$model){
          //помечаем шаблоны ключами для дальнейшей обработки
          self::setTemplatesKeys($model);

          //добавляем теги
          $placeholders[] = '{'.$model->command.'}';
          $vals[] = $model->code_before;

          if( $model->pair ){
            //добавляем парные теги
            $a[] = $tagNum;
            //добавляем закрывающие теги
            $placeholders[] = '{/'.$model->command.'}';
            $vals[] = $model->code_after;

            $tags[$model->command] = [
              'open'  => '{'.$model->command.'[^}]*}',
              'close' => '{/'.$model->command.'}',
            ];
            $tagNum ++;
          }else{
            //добавляем одиночные теги
            $b[] = $tagNum;
          }

          $tagNum ++;
        }
    }
}
