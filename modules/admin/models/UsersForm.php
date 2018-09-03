<?php

namespace app\modules\admin\models;

use Yii;
use app\models\Users;
use app\models\TimeLimitsUsers;


/**
 * Модель для проверки и сохранения данных юзера, в том числе динамических полей (лимиты)
 */
class UsersForm extends Users
{
    public $defaultLimits = false;
    public $type = "";

    public function __get($name)
    {
        if (isset($this->limits[$name])) {
            return $this->limits[$name]["time"] ?? $this->limits[$name]["default"];
        }
        return parent::__get($name);
    }

    public function __set($name, $value)
    {
        if (isset($this->limits[$name])) {
          return @$this->limitsCache[$name]["time"] = $value;
        }
        return parent::__set($name, $value);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $dynamicRules = [
            [array_keys($this->limits), 'integer'],
            [['defaultLimits'], 'boolean'],
        ];
        return array_merge(parent::rules(), $dynamicRules);
    }

    public function attributeLabels()
    {
        $dynamicAttributes = [
          'defaultLimits' => 'Использовать стандартные',
        ];
        foreach( $this->limits as $key=>$item ){
          $dynamicAttributes[$key] = $item["description"];
        }

        return array_merge(parent::attributeLabels(), $dynamicAttributes);
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {

            $needSaveLimits = false;
            foreach( $this->limits as $item ){
              if( $item["default"] != $item["time"] ){
                $needSaveLimits = true;
                break;
              }
            }

            TimeLimitsUsers::deleteAll(['user_id' => $this->id]);

            if( $needSaveLimits && !$this->defaultLimits ){
              foreach( $this->limits as $item ){
                $model = new TimeLimitsUsers();
                $model->user_id = $this->id;
                $model->limit_id = $item["id"];
                $model->time = $item["time"];
                $model->save();
              }
            }

            return true;
        }
        return false;
    }
}
