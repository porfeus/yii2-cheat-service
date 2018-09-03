<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "users".
 *
 * @property int $id ID юзера
 * @property string $login Логин юзера
 * @property string $pass Пароль юзера (md5(pass).соль)
 * @property string $fio ФИО
 * @property string $v Блокировка (да/нет)
 * @property string $email E-mail
 * @property double $coef1 Коэф-т юзера на покупку
 * @property string $r Webmoney R
 * @property double $balans Баланс рубли
 * @property string $trafbalans Баланс реалов
 * @property int $type Вид панели
 * @property double $koef Скидка покупку пакетов
 * @property double $procent Доп.реалы при покупке (%)
 * @property string $info Личная информация
 * @property string $ses Сессия №1
 * @property string $ip IP (нужен позже)
 * @property string $unicses Сессия №2
 * @property string $news Смотрел новости (да/нет)
 * @property int $pay Оплачивал (да/нет)
 * @property int $s1i2 Не понятно что это
 * @property string $notify Уведомление №1
 * @property int $notify_send Уведомление №2
 * @property int $reseller не нужен
 * @property string $reseller_data не нужен
 * @property int $reseller_show не нужен
 * @property int $lastdate не нужен
 * @property double $coef2 не нужен
 * @property double $coef3 не нужен
 * @property string $percent не нужен
 * @property double $unikjet не нужен
 * @property double $krbalans не нужен
 * @property string $pv не нужен
 * @property string $ppip не нужен
 * @property string $date не нужен
 * @property string $ppv не нужен
 * @property string $kap не нужен
 * @property int $time не нужен
 * @property string $nip не нужен
 * @property string $nkap не нужен
 * @property string $ppr не нужен
 * @property string $prich не нужен
 * @property string $z не нужен
 */
class Users extends \yii\db\ActiveRecord
{

    const RULES_LOGIN_PATTERN = '/^[a-z0-9-_]{4,20}$/iu';

    const RULES_PASS_PATTERN = '/^[a-z0-9\!@#$%\^&\*\(\)_+\-=\.?,<>\|]{6,20}$/iu';

    const RULES_INFO_MAX_VALUE = 256;

    const NOTIFY_DEFAULT_SETTINGS = array(
      "onmail1" => 1,
      "onmail2" => 1,
      "mail1" => '',
      "mail2" => '',
      "ontel1" => 0,
      "ontel2" => 0,
      "tel1" => '',
      "tel2" => '',
      "traf1" => 1000,
      "traf2" => 100,
      "limit" => 1,
    );

    public static $_notifyParams = false;
    public $limitsCache = false;
    public $settingsCache = false;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'users';
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {

            // Если амин установил новый пароль для юзера
            if( $this->isAttributeChanged('clearpass') && !empty($this->clearpass) ){

              $key = Conf::getParams('solkey');
              $pass = $this->clearpass;
              $pass_md5 = md5($pass . $key);

              $this->pass = $pass_md5;
            }

            return true;
        }
        return false;
    }

    /**
     * get statistics data
     */
    public function getSettings()
    {
        if( !$this->settingsCache ){
          $this->settingsCache = Jetid::find()
            ->where(['uz' => $this->id ])
            ->indexBy('id')
            ->orderBy(['ai' => SORT_ASC])
            ->all();
        }
        return $this->settingsCache;
    }

    /**
     * count statistics data
     */
    public function countSettings()
    {
        return Jetid::find()->where(['uz' => $this->id ])->count();
    }

    /**
     * search user by statistics id
     */
    public static function searchUserBySettingsId($id){
      $model = Jetid::find()->where(['id' => $id])->one();
      if( isset($model) ){
        return $model->userInfo;
      }
      return false;
    }

    /**
     * get limits array
     */
    public function getLimits()
    {
        if( !$this->limitsCache ){
          $defaultLimits = TimeLimits::find()
            ->indexBy('id')
            ->asArray()
            ->all();

          $userLimits = TimeLimitsUsers::find()
            ->where(['user_id'=>$this->id])
            ->indexBy('limit_id')
            ->asArray()
            ->all();

          $unionLimits = array();
          foreach($defaultLimits as $key=>$val){
            if( !empty($userLimits[$key]) ){
              $unionLimits["limit_".$key] = array_merge($userLimits[$key], $defaultLimits[$key]);
              $unionLimits["limit_".$key]['trueTime'] = $userLimits[$key]['time'];
            }else{
              $unionLimits["limit_".$key] = $defaultLimits[$key];
              $unionLimits["limit_".$key]['trueTime'] = $defaultLimits[$key]['default'];
            }
          }

          $this->limitsCache = $unionLimits;
        }


        return $this->limitsCache;
    }

    /**
     * get limits array, key = type
     */
    public function getLimitsByType(){
      $limitsByType = [];

      foreach( $this->limits as $key=>$val ){
        $limitsByType[ $val['type'] ] = $val;
      }

      return $limitsByType;
    }

    /**
     * get last request
     */
    public function lastRequest($type){
      $model = ApiRequests::find()
        ->where(['user_id' => $this->id, 'type' => $type])
        ->orderBy('last_request DESC')
        ->asArray()
        ->one();

      if( empty($model) ) return '';
      return $model['last_request'];
    }

    /**
     * обнуляем выбранную статистику
     */
    public function nullLastRequest($type){
      $this->updateLastRequest($type, '0');
    }

    /**
     * помечаем выбранную статистику временем
     */
    public function updateLastRequest($type, $last_request = 'time'){
      $model = ApiRequests::find()
        ->where(['user_id' => $this->id, 'type' => $type])
        ->orderBy('last_request DESC')
        ->one();

      if( empty($model) ){
        $model = new ApiRequests();
        $model->user_id = $this->id;
        $model->type = $type;
      }

      if( $last_request == 'time' ){
        $model->last_request = time();
      }else{
        $model->last_request = $last_request;
      }
      $model->save(false);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            /*[['login', 'pass', 'fio', 'v', 'email', 'coef1', 'r', 'balans', 'trafbalans', 'type', 'procent', 'info', 'ses', 'ip', 'unicses', 'news', 'pay', 's1i2', 'notify', 'notify_send', 'reseller', 'reseller_data', 'reseller_show', 'lastdate', 'coef2', 'coef3', 'percent', 'unikjet', 'krbalans', 'pv', 'ppip', 'date', 'ppv', 'kap', 'time', 'nip', 'nkap', 'ppr', 'prich', 'z'], 'required'],*/
            [['fio', 'email', 'info', 'notify', 'reseller_data', 'prich'], 'string'],
            [['coef1', 'balans', 'koef', 'procent', 'coef2', 'coef3', 'unikjet', 'krbalans'], 'number'],
            [['trafbalans', 'pay', 's1i2', 'notify_send', 'reseller_show', 'lastdate', 'time', 'z'], 'integer'],
            [['login', 'pass', 'clearpass', 'ses'], 'string', 'max' => 256],
            [['v', 'reseller'], 'string', 'max' => 1],
            [['r'], 'string', 'max' => 16],
            [['type'], 'string', 'max' => 4],
            // [['idjet'], 'string', 'max' => 10000],
            [['ip', 'unicses', 'ppip', 'date', 'nip', 'ppr'], 'string', 'max' => 32],
            [['news'], 'string', 'max' => 11],
            [['percent'], 'string', 'max' => 12],
            [['pv', 'kap'], 'string', 'max' => 20],
            [['ppv'], 'string', 'max' => 64],
            [['nkap'], 'string', 'max' => 2],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'login' => 'Логин',
            'pass' => 'Пароль юзера (md5(pass).соль)',
            'clearpass' => 'Пароль юзера (чистый)',
            'fio' => 'ФИО',
            'v' => 'Блокировка (да/нет)',
            'email' => 'Email',
            'coef1' => 'Коэф-т юзера на покупку (0 - по умолчанию)',
            'r' => 'WebMoney R',
            'balans' => 'Баланс рубли',
            'trafbalans' => 'Баланс реалов',
            'type' => 'Вид панели',
            'koef' => 'Скидка покупку пакетов (от 0 до 100)',
            'procent' => 'Доп.реалы при покупке (%)',
            'idjet' => 'ID настроек',
            'info' => 'Личная информация',
            'ses' => 'Сессия №1',
            'ip' => 'Ip юзера (нужен позже)',
            'unicses' => 'Сессия №2',
            'news' => 'Смотрел новости (да/нет)',
            'pay' => 'Оплачивал (да/нет)',
            's1i2' => 'Не понятно что это',
            'notify' => 'Уведомление №1',
            'notify_send' => 'Уведомление №2',
            'reseller' => 'не нужен',
            'reseller_data' => 'не нужен',
            'reseller_show' => 'не нужен',
            'lastdate' => 'Последний вход (в секундах)',
            'coef2' => 'не нужен',
            'coef3' => 'не нужен',
            'percent' => 'не нужен',
            'unikjet' => 'не нужен',
            'krbalans' => 'не нужен',
            'pv' => 'Последний вход',
            'ppip' => 'не нужен',
            'date' => 'Дата регистрации',
            'ppv' => 'не нужен',
            'kap' => 'не нужен',
            'time' => 'не нужен',
            'nip' => 'не нужен',
            'nkap' => 'не нужен',
            'ppr' => 'не нужен',
            'prich' => 'не нужен',
            'z' => 'не нужен',
        ];
    }

    /**
     * @inheritdoc
     */
    public static function getNotifyParam($param)
    {
      if( self::$_notifyParams === false ){
        self::$_notifyParams = unserialize(Yii::$app->user->identity->notify);
      }
      return self::$_notifyParams[$param] ?? self::NOTIFY_DEFAULT_SETTINGS[$param];
    }

    /**
     * @inheritdoc
     */
    public static function addUser($model)
    {
        $key = Conf::getParams('solkey');
        $md5pass = md5($model->pass . $key);
        $unicses = md5(uniqid(rand(), 1));
        $ip = Yii::$app->getRequest()->getUserIP();
        $sesb = md5($md5pass . $unicses . $key);
        $ses = md5($sesb . $key);
        $date = date("Y-m-d H:i:s");

        $notifySettings = self::NOTIFY_DEFAULT_SETTINGS;
        $notifySettings["mail1"] = $model->email;
        $notifySettings["mail2"] = $model->email;
        $notify = serialize($notifySettings);

        $user = new static();
        $user->notify = $notify;
        $user->login = $model->login;
        $user->pass = $md5pass;
        $user->fio = $model->fio;
        $user->v = 1;
        $user->email = $model->email;
        $user->type = 0;
        $user->date = $date;
        $user->pv = $date;
        $user->lastdate = time();
        $user->info = $model->info;
        $user->ses = $ses;
        $user->ip = $ip;
        $user->unicses = $unicses;
        return $user->save(false);
    }
}
