<?php

namespace app\models;

use Yii;

class UserIdentity extends Users implements \yii\web\IdentityInterface
{


    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        $user = static::findOne($id);

        if( $user && !$user->v && Yii::$app->getModule('admin')->user->isGuest ){
          Yii::$app->session->setFlash('error', "Ваш аккаунт заблокирован.");
          return null;
        }

        return $user;
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {


        return static::findOne(['unicses' => $token]);
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['login' => $username]);
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return $this->ses;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->ses === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        $key = Conf::getParams('solkey');
        return $this->pass === md5($password . $key);
    }
}
