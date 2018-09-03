<?php

namespace app\models;

use Yii;
use yii\base\Model;
use app\models\Users;

/**
 * RegForm is the model behind the login form.
 *
 * @property User|null $user This property is read-only.
 *
 */
class ProfileForm extends Model
{
    public $email;
    public $info;
    public $onmail1;
    public $mail1;
    public $ontel1;
    public $tel1;
    public $traf1;
    public $onmail2;
    public $mail2;
    public $ontel2;
    public $tel2;
    public $traf2;
    public $limit;
    public $pass;
    public $pass1;
    public $pass2;


    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
          [['email'], 'default', 'value'=>Yii::$app->user->identity->email],
          [['onmail1'], 'default', 'value'=>Users::getNotifyParam('onmail1') ],
          [['mail1'], 'default', 'value'=>Users::getNotifyParam('mail1') ],
          [['ontel1'], 'default', 'value'=>Users::getNotifyParam('ontel1') ],
          [['tel1'], 'default', 'value'=>Users::getNotifyParam('tel1') ],
          [['traf1'], 'default', 'value'=>Users::getNotifyParam('traf1') ],
          [['onmail2'], 'default', 'value'=>Users::getNotifyParam('onmail2') ],
          [['mail2'], 'default', 'value'=>Users::getNotifyParam('mail2') ],
          [['ontel2'], 'default', 'value'=>Users::getNotifyParam('ontel2') ],
          [['tel2'], 'default', 'value'=>Users::getNotifyParam('tel2') ],
          [['traf2'], 'default', 'value'=>Users::getNotifyParam('traf2') ],
          [['limit'], 'default', 'value'=>Users::getNotifyParam('limit') ],

          [['email', 'mail1', 'mail2'], 'email', 'message' => 'Проверьте правильность почтовых ящиков.'],
          [['email'], 'checkEmailExists'],
          [['info'], 'string', 'max' => Users::RULES_INFO_MAX_VALUE],
          [['tel1', 'tel2'], 'checkPhone', 'message' => 'Проверьте правильность телефонов.'],
          [['traf1', 'traf2', 'limit'], 'integer', 'message' => 'Значение «{value}» должно быть целым числом и больше нуля.'],
          [['traf1', 'traf2', 'limit'], 'compare', 'compareValue' => 1, 'operator' => '>=', 'type' => 'number', 'message' => 'Значение «{value}» должно быть целым числом и больше нуля.'],
          [['onmail1', 'onmail2', 'ontel1', 'ontel2'], 'boolean', 'message' => 'Галочки могут иметь лишь два состояния.'],
          [['pass', 'pass1'], 'required', 'when' => function($model) {
              return Yii::$app->request->post('change_password') !== null
              && empty($model->pass) && empty($model->pass1);
          }, 'message' => 'Укажите старый и новый пароль.'],
          ['pass', 'required', 'when' => function($model) {
              return !empty($model->pass1);
          }, 'message' => 'Укажите старый пароль.'],

          ['pass1', 'required', 'when' => function($model) {
              return !empty($model->pass);
          }, 'message' => 'Укажите новый пароль.'],
          [['pass1', 'pass2'],  'match', 'pattern' => Users::RULES_PASS_PATTERN, 'message' => 'Пароль должен состоять из строчных и прописных латинских букв, цифр, спецсимволов. Минимум 6 символов.'],
          [['pass'], 'checkOldPassword'],
          ['pass1', 'compare', 'compareAttribute' => 'pass2', 'message' => 'Новый пароль и повтор нового пароля не совпадают.'],
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

    /**
     * Check email exists.
     *
     */
    public function checkEmailExists($attribute, $params)
    {
        $count = Users::find()
          ->where(['email' => $this->email])
          ->andFilterWhere(['!=', 'login', Yii::$app->user->identity->login])
          ->count();
        if( $count > 0 ){
          $this->addError($attribute, 'Пользователь с таким же email уже зарегистрирован.');
        }
    }

    /**
     * Check phones format.
     *
     */
    public function checkPhone($attribute, $params)
    {
        $phone = preg_replace('@[^0-9]+@', '', $this->$attribute);
        if( mb_strlen($phone) != 11 ){
          $this->addError($attribute, 'Указан неверный номер телефона.');
        }
    }

    /**
     * Check password.
     *
     */
    public function checkOldPassword($attribute, $params)
    {
        $user = UserIdentity::findByUsername(Yii::$app->user->identity->login);

        if (!$user->validatePassword($this->$attribute)) {
            $this->addError($attribute, 'Старый пароль некорректен.');
        }
    }


    /**
     * Prepare notify
     */
    public function prepareNotify()
    {
      $notify = array();
      $attributes = array_keys(Users::NOTIFY_DEFAULT_SETTINGS);
      foreach( $attributes as $key ){
        if( !isset($this->$key) ) continue;
        $notify[$key] = $this->$key;
      }
      return serialize($notify);
    }


    /**
     * Save user data
     */
    public function saveUser()
    {
      $usersModel = Users::findOne(['id'=>Yii::$app->user->id]);

      $usersModel->notify = $this->prepareNotify();
      $usersModel->email = $this->email;
      $usersModel->info = $this->info;
      if( !empty($this->pass1) ){
        $usersModel->pass = md5($this->pass1 . Conf::getParams('solkey'));
      }

      return $usersModel->save(false);
    }


    /**
     * Set form values
     */
    public function loadModel()
    {

      $this->email = Yii::$app->user->identity->email;
      $this->info = Yii::$app->user->identity->info;
      $this->onmail1 = Users::getNotifyParam('onmail1');
      $this->mail1 = Users::getNotifyParam('mail1');
      $this->ontel1 = Users::getNotifyParam('ontel1');
      $this->tel1 = Users::getNotifyParam('tel1');
      $this->traf1 = Users::getNotifyParam('traf1');
      $this->onmail2 = Users::getNotifyParam('onmail2');
      $this->mail2 = Users::getNotifyParam('mail2');
      $this->ontel2 = Users::getNotifyParam('ontel2');
      $this->tel2 = Users::getNotifyParam('tel2');
      $this->traf2 = Users::getNotifyParam('traf2');
      $this->limit = Users::getNotifyParam('limit');

      //If manual clear notify cell in database
      if( empty($this->mail1) ) $this->mail1 = $this->email;
      if( empty($this->mail2) ) $this->mail2 = $this->email;
    }
}
