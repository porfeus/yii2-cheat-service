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
class RegForm extends Model
{
    public $login;
    public $email;
    public $pass;
    public $pass2;
    public $fio;
    public $info;
    public $verifyCode;

    private $_user = false;


    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // trim data
            [['login', 'email', 'pass', 'pass2', 'fio', 'info'], 'trim'],
            // data required
            [['login', 'email', 'pass', 'pass2', 'verifyCode'], 'required', 'message' => 'Заполните обязательные поля!'],
            //check login format
            [['login'],  'match', 'pattern' => Users::RULES_LOGIN_PATTERN, 'message' => 'Логин должен сожержать только цифры, буквы латинского алфавита и знаки "-" и "_", а так же быть не менее 4х символов и не более 20.' ],
            // check login exists
            ['login', 'checkLoginExists'],
            //set max length
            [['email', 'fio', 'info'], 'string', 'max' => Users::RULES_INFO_MAX_VALUE],
            // check email exists
            ['email', 'checkEmailExists'],
            //check email format
            ['email', 'email', 'message' => 'Проверьте правильность почтового ящика.'],
            //check password format
            [['pass'],  'match', 'pattern' => Users::RULES_PASS_PATTERN, 'message' => 'Пароль должен состоять из строчных и прописных латинских букв, цифр, спецсимволов. Минимум 6 символов.'],
            // check password compare
            ['pass2', 'comparePasswords'],
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
     * Check login exists.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function checkLoginExists($attribute, $params)
    {
        $count = Users::find()
          ->where(['login' => $this->login])
          ->count();
        if( $count > 0 ){
          $this->addError($attribute, 'Логин уже занят.');
        }
    }

    /**
     * Check email exists.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function checkEmailExists($attribute, $params)
    {
        $count = Users::find()
          ->where(['email' => $this->email])
          ->count();
        if( $count > 0 ){
          $this->addError($attribute, 'Пользователь с таким же email уже зарегистрирован.');
        }
    }

    /**
     * Compare passwords.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function comparePasswords($attribute, $params)
    {
        if( $this->pass !== $this->pass2 ){
          $this->addError($attribute, 'Пароли не совпадают.');
        }
    }

    /**
     * Logs in a user using the provided username and password.
     * @return bool whether the user is logged in successfully
     */
    public function login($validate = true)
    {
        if (!$validate || $this->validate()) {
            return Yii::$app->user->login($this->getUser(), 0);
        }
        return false;
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = UserIdentity::findByUsername($this->login);
        }

        return $this->_user;
    }
}
