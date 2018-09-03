<?php

namespace app\models;

use Yii;
use yii\base\Model;

class ScheduleForm extends Schedule
{
  /**
   * @id = setting id
   */
  static function prepareUserOutputData($id, $admin = false){
    $model = Schedule::findOne(['site_id' => $id]);

    $mysites = Jetid::find();
    $mysites = $mysites->where(['!=', 'id', $id]);
    if( !$admin ){
      $mysites = $mysites->andWhere(['uz' => Yii::$app->user->id]);
    }else{
      $mysites = $mysites->andWhere(['uz' => Yii::$app->request->get('user_id', 0)]);
    }
    $mysites = $mysites->asArray();
    $mysites = $mysites->all();

    if( $model ){
      $site = $model->toArray();

      $_pkhr = unserialize($site['pkhr']);
      $_pktm = unserialize($site['pktm']);
      $_pktml = unserialize($site['pktml']);

      //"Разбираем" параметры
      foreach ($_pkhr as $k => &$v) {
          $v = explode(":", $v);

          foreach ($v as $v1) {
              if ($v1 > 0) {
                  if( empty($all_pokaz[$k]) ) $all_pokaz[$k] = 0;
                  $all_pokaz[$k] += $v1;
              }
          }
      }

      foreach ($_pkhr[1] as $vv) {
          if ($vv == 0) {
              $vv = 0.1;
          }

          $allspk[] = $vv;
      }

      foreach ($_pktm as $k => &$v) {
          $v = explode(":", $v);
          foreach ($v as &$v1) {
              $v1 = explode("-", $v1);
          }
      }
      unset($v, $v1);

      foreach ($_pktml as $k => &$v) {
          $v = explode(":", $v);

          foreach ($v as $k1 => &$v1) {
              $v1 = explode(";", $v1);
              $tmlmin[$k][$k1][0] = $v1[0];
              $tmlmin[$k][$k1][1] = $v1[1];

              $tmlmax[$k][$k1][0] = $v1[2];
              $tmlmax[$k][$k1][1] = $v1[3];

              $tmlrefresh[$k][$k1] = $v1[4];

              $tmlminc[$k][$k1][0] = $v1[5];
              $tmlminc[$k][$k1][1] = $v1[6];

              $tmlmaxc[$k][$k1][0] = $v1[7];
              $tmlmaxc[$k][$k1][1] = $v1[8];
          }
      }

      $datas_start = implode(",", $allspk);
    }

    return array(
      'id' => $id,
      'site' => $site ?? [],
      'mysites' => $mysites,
      '_pkhr' => $_pkhr ?? [],
      '_pktm' => $_pktm ?? [],
      'all_pokaz' => $all_pokaz ?? [],
      'tmlmin' => $tmlmin ?? [],
      'tmlmax' => $tmlmax ?? [],
      'tmlrefresh' => $tmlrefresh ?? [],
      'tmlminc' => $tmlminc ?? [],
      'tmlmaxc' => $tmlmaxc ?? [],
      'datas_start' => $datas_start ?? '',
    );
  }

  /**
   * @id = setting id
   */
  static function saveForm($id, $admin = false){

    for ($i = 0; $i < 7; $i++) {
    	for ($ii = 0; $ii < 24; $ii++) {
    		$_pkhr[$i][] = $_POST['pkhr'][$i][$ii];
    		if ($_POST['pktm'][$i][$ii][0] ?? false && $_POST['pktm'][$i][$ii][1] ?? false) {
    			$pktm_curr = $_POST['pktm'][$i][$ii][0] . "-" . $_POST['pktm'][$i][$ii][1];
    		}
    		elseif ($_POST['pktm'][$i][$ii][0] ?? false) {
    			$pktm_curr = $_POST['pktm'][$i][$ii][0] . "-";
    		}
    		elseif ($_POST['pktm'][$i][$ii][1] ?? false) {
    			$pktm_curr = "-" . $_POST['pktm'][$i][$ii][1];
    		}
    		else {
    			$pktm_curr = "";
    		}

    		$_pktm[$i][] = $pktm_curr;
    		$pktml_tmp[] = $_POST['tmlmin'][$i][$ii][0] ?? '';
    		$pktml_tmp[] = $_POST['tmlmin'][$i][$ii][1] ?? '';
    		$pktml_tmp[] = $_POST['tmlmax'][$i][$ii][0] ?? '';
    		$pktml_tmp[] = $_POST['tmlmax'][$i][$ii][1] ?? '';
    		$pktml_tmp[] = $_POST['tmlrefresh'][$i][$ii] ?? '';
    		$pktml_tmp[] = $_POST['tmlminc'][$i][$ii][0] ?? '';
    		$pktml_tmp[] = $_POST['tmlminc'][$i][$ii][1] ?? '';
    		$pktml_tmp[] = $_POST['tmlmaxc'][$i][$ii][0] ?? '';
    		$pktml_tmp[] = $_POST['tmlmaxc'][$i][$ii][1] ?? '';
    		$_pktml[$i][] = implode(";", $pktml_tmp);
    		$pktml_tmp = array();
    	}

    	$_pkhr[$i][] = $_POST['pkd'][$i];
    	$_pkhr[$i] = implode(":", $_pkhr[$i]);
    	$_pktm[$i] = implode(":", $_pktm[$i]);
    	$_pktml[$i] = implode(":", $_pktml[$i]);
    }

    $_POST['copy'][$id] = 1;

    foreach($_POST['copy'] as $id => $v) {

      //safe selection
      $model = self::find();
      $model = $model->leftJoin('jetid', '`jetid`.`id` = `schedule`.`site_id`');
      $model = $model->where(['schedule.site_id' => $id]);
      if( !$admin ){
        $model = $model->andFilterWhere(['jetid.uz' => Yii::$app->user->id]);
      }
      $model = $model->one();

      if( !$model ){
        //security check
        if( !$admin && !Jetid::findOne(['id' => $id, 'uz' => Yii::$app->user->id]) ){
          continue;
        }

        $model = new Schedule();
        $model->site_id = $id;
      }

      $model->pkhr = serialize($_pkhr);
      $model->pktm = serialize($_pktm);
      $model->pktml = serialize($_pktml);
      $model->last_upd = date('Y-m-d H:i:s');
      $model->curr_hour =  '';
      $model->save(false);
    }
  }
}
