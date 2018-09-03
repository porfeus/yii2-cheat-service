<?php

namespace app\models;
use Yii;
use yii\base\Model;
use app\models\Users;



/**
 * LoginForm is the model behind the login form.
 *
 * @property User|null $user This property is read-only.
 *
 */
class NewpassForm  extends Users
{


    public $login;
    public $email;
    public $verifyCode;
    /**
     * @return array the validation rules.
     */
   public function rules()
  {
      return [
          // атрибут required указывает, что name, email, subject, body обязательны для заполнения
          [['login', 'email', 'verifyCode'], 'required', 'message' => 'Заполните обязательные поля!'],
          // check user exists
          ['login', 'checkUserExists'],
          // verifyCode needs to be entered correctly
          ['verifyCode', 'checkCaptcha'],

      ];
  }

  /**
   * Check captcha.
   *
   * @param string $attribute the attribute currently being validated
   * @param array $params the additional name-value pairs given in the rule
   */
  public function checkCaptcha($attribute, $params)
  {
      $session = Yii::$app->session;
      if( $session['securimage_code_value']['default'] != $this->verifyCode ){
        $this->addError($attribute, 'Проверочный пример решен неверно.');
      }
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
   * Check user exists.
   *
   * @param string $attribute the attribute currently being validated
   * @param array $params the additional name-value pairs given in the rule
   */
  public function checkUserExists($attribute, $params)
  {
      $count = Users::find()
        ->where(['login' => $this->login, 'email' => $this->email])
        ->count();
      if( !$count ){
        $this->addError($attribute, 'Пользователь с такими данными не найден или допущена ошибка!');
      }
  }

}
