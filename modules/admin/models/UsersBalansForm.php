<?php

namespace app\modules\admin\models;

use Yii;
use yii\base\Model;
use app\models\Logs;
use app\models\Paymentsinfo;
use app\models\Antifraud;


class UsersBalansForm extends Model
{
  public $currency;
  public $type;
  public $amount;
  public $comment = 'Возврат средств';

  public $currencyVariants = [
    '1' => 'Рубли',
    '2' => 'Реалы',
  ];

  public $typeVariants = [
    '1' => 'Зачисление средств',
    '2' => 'Списание средств',
  ];

  /**
   * @inheritdoc
   */
  public function rules()
  {
      return [
        [['currency', 'type', 'amount', 'comment'], 'required'],
        [['amount'], 'integer'],
        [['comment'], 'string', 'max' => 256],
      ];
  }

  /**
   * @inheritdoc
   */
  public function attributeLabels()
  {
      return [
          'currency' => 'Валюта',
          'type' => 'Операция',
          'amount' => 'Сумма',
          'comment' => 'Комментарий',
      ];
  }

  /**
   * @inheritdoc
   */
  public function changeBalans($userModel)
  {
    $balans_before = $userModel->balans;
    $trafbalans_before = $userModel->trafbalans;

    if( $this->currency == 1 ){ // рубли
      if( $this->type == 1 ){
        $userModel->balans += $this->amount;

        //записываем в историю транзакций
        Paymentsinfo::add([
          'userid' => $userModel->id,
          'cost' => $this->amount,
          'note' => "Комментарий: {$this->comment}. Было рублей: {$balans_before}, стало: {$userModel->balans}. Было реалов: {$trafbalans_before}, стало: {$userModel->trafbalans}",
        ], Paymentsinfo::BALANS_ADMIN_PLUS);

        //проверка на мошенничество
        Antifraud::add([
          'userid' => $userModel->id,
          'type' => Antifraud::BALANS_ADMIN_PLUS,
          'cost' => $this->amount,
          'note' => "Комментарий: {$this->comment}. Было реалов: {$trafbalans_before}, стало: {$userModel->trafbalans}",
        ]);

        Logs::AddTransactionLogs("ID пользователя: {$userModel->id}, админ пополнил основной баланс рублей на " .
          round($this->amount, 2) . " руб., комментарий: {$this->comment}", $userModel->id);
      }else{
        $userModel->balans -= $this->amount;

        //записываем в историю транзакций
        Paymentsinfo::add([
          'userid' => $userModel->id,
          'cost' => $this->amount,
          'note' => "Комментарий: {$this->comment}. Было рублей: {$balans_before}, стало: {$userModel->balans}. Было реалов: {$trafbalans_before}, стало: {$userModel->trafbalans}",
        ], Paymentsinfo::BALANS_ADMIN_MINUS);

        //проверка на мошенничество
        Antifraud::add([
          'userid' => $userModel->id,
          'type' => Antifraud::BALANS_ADMIN_MINUS,
          'cost' => $this->amount,
          'note' => "Комментарий: {$this->comment}. Было реалов: {$trafbalans_before}, стало: {$userModel->trafbalans}",
        ]);

        Logs::AddTransactionLogs("ID пользователя: {$userModel->id}, админ списал с основного баланса рублей " .
          round($this->amount, 2) . " руб., комментарий: {$this->comment}", $userModel->id);
      }
    }else{ //реалы
      if( $this->type == 1 ){
        $userModel->trafbalans += $this->amount;

        //записываем в историю транзакций
        Paymentsinfo::add([
          'userid' => $userModel->id,
          'cost' => $this->amount,
          'note' => "Комментарий: {$this->comment}. Было рублей: {$balans_before}, стало: {$userModel->balans}. Было реалов: {$trafbalans_before}, стало: {$userModel->trafbalans}",
        ], Paymentsinfo::TRAFBALANS_ADMIN_PLUS);

        //проверка на мошенничество
        Antifraud::add([
          'userid' => $userModel->id,
          'type' => Antifraud::TRAFBALANS_ADMIN_PLUS,
          'cost' => $this->amount,
          'note' => "Комментарий: {$this->comment}. Было реалов: {$trafbalans_before}, стало: {$userModel->trafbalans}",
        ], false);

        Logs::AddTransactionLogs("ID пользователя: {$userModel->id}, админ пополнил баланс реалов на " .
          round($this->amount, 2) . " реал., комментарий: {$this->comment}", $userModel->id);
      }else{
        $userModel->trafbalans -= $this->amount;

        //записываем в историю транзакций
        Paymentsinfo::add([
          'userid' => $userModel->id,
          'cost' => $this->amount,
          'note' => "Комментарий: {$this->comment}. Было рублей: {$balans_before}, стало: {$userModel->balans}. Было реалов: {$trafbalans_before}, стало: {$userModel->trafbalans}",
        ], Paymentsinfo::TRAFBALANS_ADMIN_MINUS);

        //проверка на мошенничество
        Antifraud::add([
          'userid' => $userModel->id,
          'type' => Antifraud::TRAFBALANS_ADMIN_MINUS,
          'cost' => $this->amount,
          'note' => "Комментарий: {$this->comment}. Было реалов: {$trafbalans_before}, стало: {$userModel->trafbalans}",
        ], false);

        Logs::AddTransactionLogs("ID пользователя: {$userModel->id}, админ списал с баланса реалов " .
          round($this->amount, 2) . " реал., комментарий: {$this->comment}", $userModel->id);
      }
    }

    //---

    $userModel->save(false);
  }
}
