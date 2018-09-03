<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "paymentsinfo".
 *
 * @property int $id
 * @property string $payment
 * @property int $userid
 * @property string $cost
 * @property int $time
 * @property string $metod
 * @property int $type
 * @property string $status
 * @property string $note
 */
class Paymentsinfo extends \yii\db\ActiveRecord
{
    const BALANS_BUY = 0; //Пополнение баланса личного кабинета
    const TRAFBALANS_BUY = 1; //Покупка пакета реалов
    const TRAFBALANS_PLUS = 2; //Списание реалов с настройки
    const TRAFBALANS_MINUS = 3; //Пополнение реалами настройки
    const TRAFBALANS_MULTIMANUAL_PLUS = 4; //Массовое списание с настроек
    const TRAFBALANS_MULTIMANUAL_MINUS = 5; //Массовое пополнение настроек
    const TRAFBALANS_DEL_PLUS = 6; //Удаление настройки
    const TRAFBALANS_MULTIAUTO_PLUS = 7; //Снятие всех реалов с настройки
    const BALANS_ADDSET_MINUS = 8; //Заказ платной настройки
    const BALANS_EDITSET_MINUS = 9; //Заказ изменения настройки
    const BALANS_ADMIN_PLUS = 10; //Добавление рублей админом
    const BALANS_ADMIN_MINUS = 11; //Списание рублей админом
    const TRAFBALANS_ADMIN_PLUS = 12; //Добавление реалов админом
    const TRAFBALANS_ADMIN_MINUS = 13; //Списание реалов админом
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'paymentsinfo';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['payment', 'userid', 'cost', 'time', 'metod', 'status'], 'required'],
            [['userid'], 'integer'],
            [['metod', 'status', 'time'], 'string'],
        ];
    }


    /**
     * set default values
     */
    public function beforeSave($insert) {
      if( parent::beforeSave($insert) ){

        if( $insert ){
          $this->time = date("Y-m-d H:i:s");
          if( empty($this->userid) ) $this->userid = Yii::$app->user->id;
          if( empty($this->status) ) $this->status = "NO";
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
            'payment' => 'Номер платежа',
            'userid' => 'ID пользователя',
            'cost' => 'Сумма',
            'time' => 'Время операции',
            'metod' => 'Платежная система',
            'type' => 'Операция',
            'status' => 'Статус',
            'note' => 'Примечание',
        ];
    }

    /**
     * Возвращает варианты типов
     */
    public function getTypeVariants()
    {
      $typeTitle = [
        self::BALANS_BUY => 'Пополнение баланса личного кабинета',
        self::TRAFBALANS_BUY => 'Покупка пакета реалов',
        self::TRAFBALANS_PLUS => 'Списание реалов с настройки',
        self::TRAFBALANS_MINUS => 'Пополнение реалами настройки',
        self::TRAFBALANS_MULTIMANUAL_PLUS => 'Массовое списание реалов',
        self::TRAFBALANS_MULTIMANUAL_MINUS => 'Массовое пополнение реалов',
        self::TRAFBALANS_DEL_PLUS => 'Удаление настройки',
        self::TRAFBALANS_MULTIAUTO_PLUS => 'Снятие всех реалов с настройки',
        self::BALANS_ADDSET_MINUS => 'Заказ платной настройки',
        self::BALANS_EDITSET_MINUS => 'Заказ изменения настройки',
        self::BALANS_ADMIN_PLUS => 'Пополнение баланса рублей админом',
        self::BALANS_ADMIN_MINUS => 'Списание баланса рублей админом',
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
      $currency = 'реал.';
      if( in_array($this->type, [
        self::BALANS_BUY,
        self::BALANS_ADDSET_MINUS,
        self::BALANS_EDITSET_MINUS,
        self::BALANS_ADMIN_PLUS,
        self::BALANS_ADMIN_MINUS,
      ]) ){
        $currency = 'руб.';
      }

      return $currency;
    }

    /**
     * Возвращает описание статуса
     */
    public function getStatusText()
    {
        $text = array(
          "OK-PAY"=>"Оплачен",
          "NO"=>"Ожидает"
        );
        return $text[ $this->status ] ?? "?";
    }

    /**
     * Возвращает цветное описание статуса
     */
    public function getColouredStatusText()
    {
        $text = array(
          "OK-PAY"=>'<font color="green">Оплачен</font>',
          "NO"=>'<font color="red">Ожидает</font>'
        );
        return $text[ $this->status ] ?? "?";
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
            self::BALANS_ADDSET_MINUS,
            self::BALANS_EDITSET_MINUS,
            self::BALANS_ADMIN_MINUS,
            self::TRAFBALANS_ADMIN_MINUS,
        ]) ){
          $color = 'red';
        }

        return '<font color="'.$color.'">'.($this->typeVariants[ $this->type ] ?? "?").'</font>';
    }

    /**
     * Создание записи в истории
     */
    public static function add($assoc, $type){
      $paymentModel = new self();
      $paymentModel->payment = rand(111111111, 999999999);
      $paymentModel->userid = $assoc['userid'] ?? Yii::$app->user->id;
      $paymentModel->cost = round($assoc['cost']);
      $paymentModel->metod = $assoc['metod'] ?? "Личный баланс";
      $paymentModel->status = "OK-PAY";
      $paymentModel->type = $type;
      $paymentModel->note = $assoc['note'] ?? '';
      $paymentModel->save(false);
    }
}
