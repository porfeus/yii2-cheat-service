<?php

namespace app\models;

use Yii;
use app\components\ApiFunctions;

/**
 * This is the model class for table "schedule".
 *
 * @property int $id
 * @property int $site_id
 * @property string $pkhr
 * @property string $pktm
 * @property string $pktml
 * @property string $curr_hour
 * @property string $last_upd
 */
class Schedule extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'schedule';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['site_id', 'pkhr', 'pktm', 'pktml', 'curr_hour'], 'required'],
            [['site_id'], 'integer'],
            [['pkhr', 'pktm', 'pktml', 'curr_hour'], 'string'],
            [['last_upd'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'site_id' => 'Site ID',
            'pkhr' => 'Pkhr',
            'pktm' => 'Pktm',
            'pktml' => 'Pktml',
            'curr_hour' => 'Curr Hour',
            'last_upd' => 'Last Upd',
        ];
    }

    public function afterDelete()
    {
       parent::afterDelete();
       ApiFunctions::SiteTaskDel(array($this->site_id));
    }

    /**
     * @id = schedule id
     */
    static function loadModel($id){
      $model = self::find()
        ->leftJoin('jetid', '`jetid`.`id` = `schedule`.`site_id`')
        ->where(['schedule.id' => $id, 'jetid.uz' => Yii::$app->user->id])
        ->one();

      if( $model ){
        return $model;
      }

      throw new \yii\web\ForbiddenHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

    public function disable($disable){
      $this->disabled = (int)$disable;
      if( $this->disabled ){
        ApiFunctions::SiteTaskDel(array($this->site_id));
        Logs::AddSettingLogs("Пользователь отключил расписание настройки #{$this->site_id}");
      }else{
        $this->last_upd = date('Y-m-d H:i:s');
        Logs::AddSettingLogs("Пользователь включил расписание настройки #{$this->site_id}");
      }
      $this->save(false);
    }
}
