<?php

namespace app\models;

use Yii;
use yii\data\ActiveDataProvider;

/**
 * This is the model class for table "tikets".
 *
 * @property int $id id тикета или сообщения
 * @property int $parent_id id тикета
 * @property string $date дата создания
 * @property int $user id юзера
 * @property int $is_support техподдержка?
 * @property string $title тема
 * @property string $message сообщение
 * @property string $files массив файлов
 * @property int $readed прочтен юзером
 * @property int $answered отвечен админом
 * @property int $archived архивирован, 1 да, 0 нет
 */
class Tikets extends \yii\db\ActiveRecord
{
    const STATUS_NEW = 0;
    const STATUS_READED = 1;
    const STATUS_ARCHIVED = 2;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tikets';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['info'], 'string', 'max' => 30, 'tooLong'=>'Информация для связи должна содержать максимум 30 символов.'],
            [['title'], 'required', 'message'=>'Введите тему сообщения.'],
            [['message'], 'required', 'message'=>'Введите текст сообщения.'],
            [['title'], 'string', 'max' => 60, 'tooLong'=>'Тема должна содержать максимум 60 символов.'],
            [['message'], 'string', 'max' => 3000, 'tooLong'=>'Сообщение должно содержать максимум 3000 символов.'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'parent_id' => 'ID темы',
            'date' => 'Дата создания',
            'user' => 'ID пользователя',
            'is_support' => 'Техподдержка?',
            'title' => 'Тема',
            'message' => 'Сообщение',
            'readed' => 'Прочтено',
            'answered' => 'Получен ответ',
            'archived' => 'Архивирован',
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
     * @inheritdoc
     */
    public function getUserInfo()
    {
       return $this->hasOne(Users::className(), ['id' => 'user']);
    }

    /**
     * @inheritdoc
     */
    public function getLogin()
    {
       return $this->userInfo->login;
    }

    /**
     * @inheritdoc
     */
    public function getTiketInfo()
    {
       return $this->hasOne(Tikets::className(), ['id' => 'parent_id']);
    }

    /**
     * Files list
     */
    public function getFilesList()
    {
       if( empty($this->files) ) return [];

       $files = unserialize($this->files);

       return $files;
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {

            //create new tiket or message
            if( $insert ){
              $this->user = Yii::$app->user->getId();
              $this->date = date("Y-m-d H:i:s");

              // user created a new tiket
              if( !$this->is_support && !$this->parent_id ){
                $this->readed = 1;
              }
            }

            // user created a new message
            if( !$this->is_support && $this->parent_id ){
              // check access
              if( $this->user != $this->tiketInfo->user ){
                return false;
              }

              $tiket = Tikets::findOne($this->parent_id);

              // tiket is not found
              if( !$tiket ){
                return false;
              }

              $tiket->answered = 0;
              $tiket->archived = 0;
              $tiket->save(false);
            }

            //support created a new message
            if( $this->is_support && $this->parent_id ){
              $tiket = Tikets::findOne($this->parent_id);
              $tiket->answered = 1;
              $tiket->readed = 0;
              $tiket->archived = 0;
              $tiket->save(false);
            }

            return true;
        }
        return false;
    }


    /**
    * Событие после удаления тикета
    */
    public function afterDelete()
     {
         parent::afterDelete();

         $storagePath = Yii::getAlias('@webroot/files');

         foreach( $this->filesList as $file ){
           $path_to_file = "$storagePath/{$file['new']}";
           if( file_exists($path_to_file) ){
             unlink($path_to_file);
           }
         }

     }

    /**
     * Возвращает статуст тикета (новый, прочитанный, архивный)
     */
    public function getStatus()
    {
      if( !$this->answered )
        return static::STATUS_NEW;
      else
      if( $this->answered )
        return static::STATUS_READED;

      return static::STATUS_ARCHIVED;
    }

    /**
     * Возвращает общее количество неотвеченных тикетов
     */
    static public function countNewTikets()
    {
      return static::find()
        ->where(['parent_id' => 0, 'answered' => 0, 'archived' => 0])
        ->count();
    }

    /**
     * Возвращает количество неотвеченных тикетов у юзера
     */
    static public function countUserNewTikets()
    {
      return static::find()
        ->where([
          'parent_id' => 0,
          'user' => Yii::$app->user->getId(),
          'answered' => 0,
          'archived' => 0
        ])
        ->count();
    }

    /**
     * Возвращает количество непрочитанных тикетов
     */
    static public function countUnreadTikets()
    {
      return static::find()
        ->where([
          'parent_id' => 0,
          'user' => Yii::$app->user->getId(),
          'readed' => 0,
          'archived' => 0,
        ])
        ->count();
    }

    /**
     * Возвращает количество тикетов юзера
     */
    static public function getUserTikets($archive = 0)
    {
      $query = static::find()
        ->where([
          'archived' => $archive,
          'parent_id' => 0,
          'user'=>Yii::$app->user->getId()
        ])
        ->orderBy(['id' => SORT_DESC]);

      $dataProvider = new ActiveDataProvider([
          'query' => $query,
      ]);

      return $dataProvider;
    }

    /**
     * Для поиска
     */
    static public function getUserTiketMessages($tiket)
    {
      $sql = 'SELECT * FROM tikets WHERE parent_id=:tiket OR id=:tiket ORDER BY id';
      $query = static::findBySql($sql, [':tiket'=>$tiket]);

      $dataProvider = new ActiveDataProvider([
          'query' => $query,
      ]);

      return $dataProvider;
    }
}
