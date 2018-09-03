<?php

namespace app\models;

use Yii;
use yii\web\UploadedFile;

/**
 * OrderForm is the model behind the login form.
 *
 *
 */
class OrderForm extends Tikets
{
    /**
     * @var UploadedFile
     */
    public $filesList;
    static $tiketTitle = [
      'mail_title' => 'Новая заявка на настройку',
      'mail_message' => 'добавление настройки',
    ];

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
       $addRules = [
           [['filesList'], 'file', 'maxFiles' => 10, 'skipOnEmpty' => true, 'checkExtensionByMimeType' => false, 'extensions' => ['docx', 'jpg', 'png', 'zip', 'rar', 'doc', 'xls', 'xlsx', 'pdf', 'txt']],
           [['info'], 'required', 'message' => 'Введите информацию для связи.'],
       ];
       return array_merge(parent::rules(), $addRules);
    }

    public function upload()
    {
        $files = array();
        if ($this->validate()) {
            foreach ($this->filesList as $file) {
                $nameNew = uniqid() . '.' . $file->extension;
                $nameOld = $file->baseName . '.' . $file->extension;
                $file->saveAs('files/' . $nameNew);

                array_push($files, array('old' => $nameOld, 'new'=>$nameNew));
            }
            $this->files = serialize($files);
            unset($this->filesList);

            return true;
        } else {
            return false;
        }
    }

    public static function onTiketCreated($model){

        Yii::$app->session->addFlash('info',
        'Ваша заявка успешно отправлена, заявку можно дополнить в тикете!');

        $tiketLink = \yii\helpers\Url::to(['admin/tikets/view', 'id'=>$model->id], true);

        Yii::$app->mailer->compose()
          ->setFrom([
            Yii::$app->params['sendFrom']['email'] => Yii::$app->params['sendFrom']['name']
          ])
          ->setReplyTo(Yii::$app->params['sendFrom']['email'])
          ->setTo( Conf::getParams('adminmail') )
          ->setSubject(static::$tiketTitle['mail_title'])
          ->setHtmlBody(
            "Была добавлена новая <a href=\"$tiketLink\">заявка на ".static::$tiketTitle['mail_message']."</a> от пользователя {$model->user} ({$model->userInfo->login})"
          )
          ->send();
    }
}
