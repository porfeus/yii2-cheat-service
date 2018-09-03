<?php

namespace app\modules\admin;

class Module extends \yii\base\Module
{
    public $controllerNamespace = 'app\modules\admin\controllers';

    public function init()
    {
        parent::init();

        // custom initialization code goes here
        \Yii::configure($this,  [
            'components' => [
              'user' => [
                'class' => 'yii\Web\User',
                'idParam' => '__adminId', //переменная в сессии
                'identityCookie' => ['name' => '_adminIdentity', 'httpOnly' => true], //переменная в куки
                'identityClass' => 'app\modules\admin\models\UserIdentity',
                'enableAutoLogin' => true,
                'loginUrl' => ['/admin/site/login'],
                'returnUrl' => ['/admin/users/index'],
              ],
            ],
        ]);
    }
}
