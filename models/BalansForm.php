<?php

namespace app\models;

use Yii;
use yii\base\Model;

class BalansForm extends Model
{

    public $tarch;
    public $mytarinp = 1000; //минимум к покупке реалов

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $tarrifs = Jettarif::find()->select('id')->asArray()->column();
        array_push($tarrifs, 'rozn');
        return [
          [['tarch'], 'in', 'range' => $tarrifs, 'message' => 'Выбранный тариф не найден.'],

          [['mytarinp'], 'required', 'when' => function($model) {
              return $model->tarch === 'rozn';
          }, 'message' => 'Укажите сумму к пополнению.'],
          [['mytarinp'], 'integer', 'min' => $this->mytarinp, 'when' => function($model) {
              return $model->tarch === 'rozn';
          }, 'tooSmall' => "Минимум к покупке {$this->mytarinp} реалов."],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'tarch' => 'Покупка внутренней валюты',
            'mytarinp' => 'Введите кол-во реалов',
        ];
    }

    /**
     * Add errors in alert.
     *
     */
    public function addError($attribute, $error = '')
    {
        parent::addError($attribute, $error);

        $flashes = Yii::$app->session->getAllFlashes();
        if( empty($flashes['error']) || !in_array($error, $flashes['error']) ){
          Yii::$app->session->addFlash('error', $error);
        }
    }
}
