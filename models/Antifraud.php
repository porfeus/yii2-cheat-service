<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "antifraud".
 *
 * @property int $id ID
 * @property string $userid ID пользователя
 * @property int $cost Сумма
 * @property string $date Время операции
 * @property string $type Операция
 * @property string $note Примечание
 */
class Antifraud extends \yii\db\ActiveRecord
{
    const TRAFBALANS_BUY = 1; //Покупка пакета реалов
    const TRAFBALANS_PLUS = 2; //Списание реалов с настройки
    const TRAFBALANS_MINUS = 3; //Пополнение реалами настройки
    const TRAFBALANS_MULTIMANUAL_PLUS = 4; //Массовое списание с настроек
    const TRAFBALANS_MULTIMANUAL_MINUS = 5; //Массовое пополнение настроек
    const TRAFBALANS_DEL_PLUS = 6; //Удаление настройки
    const TRAFBALANS_MULTIAUTO_PLUS = 7; //Снятие всех реалов с настройки
    const TRAFBALANS_ADMIN_PLUS = 12; //Добавление реалов админом
    const TRAFBALANS_ADMIN_MINUS = 13; //Списание реалов админом
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'antifraud';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['userid', 'cost', 'balance', 'date', 'type', 'note'], 'required'],
            [['cost', 'balance'], 'integer'],
            [['date'], 'safe'],
            [['note'], 'string'],
            [['userid', 'type'], 'string', 'max' => 256],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'userid' => 'ID пользователя',
            'cost' => 'Сумма',
            'balance' => 'Остаток',
            'date' => 'Время операции',
            'type' => 'Операция',
            'note' => 'Примечание',
        ];
    }


    /**
     * set default values
     */
    public function beforeSave($insert) {
      if( parent::beforeSave($insert) ){

        if( $insert ){
          $this->date = date("Y-m-d H:i:s");
        }

        return true;
      }
      return false;
    }

    /**
     * Возвращает варианты типов
     */
    public function getTypeVariants()
    {
      $typeTitle = [
        self::TRAFBALANS_BUY => 'Покупка пакета реалов',
        self::TRAFBALANS_PLUS => 'Списание реалов с настройки',
        self::TRAFBALANS_MINUS => 'Пополнение реалами настройки',
        self::TRAFBALANS_MULTIMANUAL_PLUS => 'Массовое списание реалов',
        self::TRAFBALANS_MULTIMANUAL_MINUS => 'Массовое пополнение реалов',
        self::TRAFBALANS_DEL_PLUS => 'Удаление настройки',
        self::TRAFBALANS_MULTIAUTO_PLUS => 'Снятие всех реалов с настройки',
        self::TRAFBALANS_ADMIN_PLUS => 'Пополнение баланса реалов админом',
        self::TRAFBALANS_ADMIN_MINUS => 'Списание баланса реалов админом',
      ];

      return $typeTitle;
    }

    /**
     * Возвращает описание типа
     */
    public function getTypeText()
    {
      return $this->typeVariants[$this->type] ?? '-';
    }

    /**
     * Возвращает название валюты
     */
    public function getCurrency()
    {

      return 'реал.';
    }

    /**
     * Возвращает цветное описание типа
     */
    public function getColouredTypeText()
    {
        $color = 'green';
        if( in_array($this->type, [
            self::TRAFBALANS_BUY,
            self::TRAFBALANS_MINUS,
            self::TRAFBALANS_MULTIMANUAL_MINUS,
            self::TRAFBALANS_ADMIN_MINUS,
        ]) ){
          $color = 'red';
        }

        return '<font color="'.$color.'">'.($this->typeVariants[ $this->type ] ?? "?").'</font>';
    }

    /**
     * Обновление общего баланса реалов
     */
    public static function updateMainBalans($type, $cost){

      //Выявляем отрицательную сумму
      if( in_array($type, [
        self::TRAFBALANS_MINUS,
        self::TRAFBALANS_MULTIMANUAL_MINUS,
        self::TRAFBALANS_ADMIN_MINUS,
      ]) ){
        $cost *= -1;
      }

      $balansModel = Conf::findOne(['name' => 'jetswap_trafbalans']);
      $balansModel->updateCounters(['value' => $cost]);
    }

    /**
     * Возвращает общий баланс реалов
     */
    public static function getMainBalans(){
      $balansModel = Conf::findOne(['name' => 'jetswap_trafbalans']);
      return $balansModel->value;
    }

    /**
     * Создание записи
     */
    public static function add($assoc, $updateMainBalans = true){
      $cost = round($assoc['cost']);
      $type = $assoc['type'];

      $trafbalans_before = self::getMainBalans();

      //Обновляем общий баланс реалов
      if( $updateMainBalans ){
        self::updateMainBalans($type, $cost);
      }

      $trafbalans_after = self::getMainBalans();

      $model = new self();
      $model->userid = $assoc['userid'] ?? Yii::$app->user->id;
      $model->cost = $cost;
      $model->balance = $trafbalans_after;
      $model->type = $type;
      $model->note = $assoc['note'] ?? '';

      //Добавляем в примечание инфо об общем балансе
      $model->note.= ". Было общих реалов: {$trafbalans_before}, стало: {$trafbalans_after}";

      $model->save(false);
    }
}
