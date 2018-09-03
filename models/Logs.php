<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "logs".
 *
 * @property string $id ID записи
 * @property string $text ЛОГ (данные с сайта)
 * @property string $date Дата записи
 */
class Logs extends \yii\db\ActiveRecord
{
    public $categories = [
      '0' => 'Остальное',
      '1' => 'Авторизация/Регистрация',
      '2' => 'Транзакции',
      '3' => 'Операции с настройками',
      '4' => 'Пополнение и снятие реалов с настроек',
      '5' => 'CRON',
    ];
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'logs';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['text'], 'required'],
            [['text'], 'string'],
            [['date', 'user_id', 'referer', 'browser', 'ip'], 'safe'],
            [['category'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {

            if( $insert ){
              $this->date = date('Y-m-d H:i:s');
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
            'id' => 'ID записи',
            'text' => 'ЛОГ (данные с сайта)',
            'date' => 'Дата записи',
            'category' => 'Категория',
            'user_id' => 'ID юзера',
            'ip' => 'IP-адрес',
            'browser' => 'Юзер агент',
            'referer' => 'REFERER',
        ];
    }

    /**
     * Other logs
     */
    public static function AddOtherLogs($text, $user_id = null) {
      self::AddLogs($text, $category = 0, $user_id);
    }

    /**
     * login logs
     */
    public static function AddLoginLogs($text, $user_id = null) {
      self::AddLogs($text, $category = 1, $user_id);
    }

    /**
     * Transactions logs
     */
    public static function AddTransactionLogs($text, $user_id = null) {
      self::AddLogs($text, $category = 2, $user_id);
    }

    /**
     * Settings logs
     */
    public static function AddSettingLogs($text, $user_id = null) {
      self::AddLogs($text, $category = 3, $user_id);
    }

    /**
     * Balans logs
     */
    public static function AddBalansLogs($text, $user_id = null) {
      self::AddLogs($text, $category = 4, $user_id);
    }

    /**
     * Cron logs
     */
    public static function AddCronLogs($text, $user_id = null) {
      self::AddLogs($text, $category = 5, $user_id);
    }

    /**
     * simple add log
     */
    public static function AddLogs($text, $category = 0, $user_id = null) {

      $model = new Logs;
      $model->text = $text;
      $model->date = date('Y-m-d H:i:s');

      $model->category = $category;
      $model->user_id = $user_id ?? Yii::$app->user->id ?? '';
      $model->ip = getenv("REMOTE_ADDR");
      $model->browser = getenv("HTTP_USER_AGENT");
      $model->referer = ($_SERVER['HTTP_REFERER'] ?? '');
      $model->save();
    }
}
