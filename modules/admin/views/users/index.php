<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\grid\GridView;
use app\models\Schedule;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\admin\models\UsersSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Users');
$this->params['breadcrumbs'][] = $this->title;


$this->registerCss("
.tableWithoutBorders{
  border: none;
  margin: 0;
  padding: 0;
}
.tableWithoutBorders tr:first-child td {
  border-top: none !important;
}
.tableWithoutBorders tr td:last-child {
  border-right: none !important;
}
.tableWithoutBorders tr:last-child td {
  border-bottom: none !important;
}
.tableWithoutBorders tr td:first-child {
  border-left: none !important;
}
.tableWithoutBorders td{
  text-align: center;
}
.container{
  margin: 0 !important;
  width: 100%;
}
.break-all{
  word-break: break-all !important;
}
");

$script = <<< JS
  jQuery('.robot-add').on('click', function(e){
    e.preventDefault();
    if( $(this).next().hasClass('robot-add-form') ){
      $(this).next().remove();
    }else{
      var selectOptions = $('#templates_station select').clone().html();
      $(this).after([
        '<form action="/admin/users/robot-add" class="robot-add-form" style="margin-top:10px;" method="get" target="_blank">',
        '<input type="hidden" name="user_id" value="'+ $(this).attr('href') +'" />',
        '<div class="form-group">',
        '<select name="type" class="form-control">',
        selectOptions,
        '</select>',
        '</div>',
        '<button class="btn btn-success" onclick="this.form.submit()">ОК</button>',
        '</form>'
      ].join(''));
    }
  });
JS;
$this->registerJs($script, yii\web\View::POS_READY);
?>

<div style="display: none" id="templates_station">
  <select>
    <?php
    foreach( $templates as $template ){
      echo '<option value="'.$template['url'].'">'.$template['name'].'</option>';
    }
    ?>
  </select>
</div>

<div class="clearfix"></div>

<div class="row">
  <div class="col-md-5" style="margin-left: 10px;">
    Всего пользователей в системе: <?php echo $all_users; ?> человек<br>
    Всего рублей на счетах пользователей: <?php echo $all_balance_rub; ?> рублей<br>
    Всего реалов на счетах пользователей: <?php echo $all_balance_traf; ?> штук<br><br>
  </div>
  <div class="col-md-6">
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>
  </div>
</div>

<div class="clearfix"></div>

<div class="users-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?//= Html::a(Yii::t('app', 'Create Users'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
              'attribute' => 'id',
              'headerOptions' => [
                'style' => 'width: 75px;',
              ],
            ],
            [
              'attribute' => 'login',
              'headerOptions' => [
                'style' => 'width: 100px;',
              ],
            ],
            [
              'attribute' => 'fio',
              'headerOptions' => [
                'style' => 'width: 100px;',
              ],
            ],
            [
              'label' => 'Почта',
              'attribute' => 'email',
              'format' => 'html',
              'contentOptions' => [
                'class' => 'break-all'
              ],
              'value' => function($model){
                return '<div style="width: 100px;" class="break-all">
                '.Html::encode($model->email).'
                </div>';
              }
            ],
            [
              'label' => 'Баланс',
              'format' => 'html',
              'value' => function($model){
                $table =
                '<table class="table table-striped table-bordered tableWithoutBorders">
                <tr>
                  <td style="text-align: left">
                  Реалы: <br />'.$model->trafbalans.'
                  </td>
                </tr>
                <tr>
                  <td style="text-align: left">
                  Рубли: <br />'.$model->balans.'
                  </td>
                </tr>
                </table>';
                return $table;
              },
              'headerOptions' => [
              //  'style' => 'width: 150px;',
              ],
            ],
            [
              'label' => 'Тарифы юзера',
              'format' => 'html',
              'value' => function($model){
                $table =
                '<table class="table table-striped table-bordered tableWithoutBorders">
                <tr>
                  <td style="text-align: left">
                  Коэфф-т: '.$model->coef1.'
                  </td>
                </tr>
                <tr>
                  <td style="text-align: left">
                  Скидка на пакеты: '.$model->koef.'%
                  </td>
                </tr>
                <tr>
                  <td style="text-align: left">
                  Доп.реалы: +'.$model->procent.'%
                  </td>
                </tr>
                </table>';
                return $table;
              },
              'headerOptions' => [
              //  'style' => 'width: 200px;',
              ],
            ],
            [
              'header' => '
              <div style="width: 450px;">
              <table class="table table-striped table-bordered tableWithoutBorders">
              <tr>
                <td colspan="6">
                Статистика
                </td>
              </tr>
              <tr>
                <td style="width: 75px; vertical-align: bottom">
                ID <br />настр.
                </td>
                <td style="width: 55px; vertical-align: bottom">
                Час
                </td>
                <td style="width: 55px; vertical-align: bottom">
                День
                </td>
                <td style="width: 55px; vertical-align: bottom">
                Всего
                </td>
                <td style="width: 85px; vertical-align: bottom">
                Баланс <br />(реалы)
                </td>
                <td style="width: 125px; vertical-align: bottom">
                Параметры
                </td>
              </tr>
              </table>
              </div>
              ',
              'headerOptions' => [
              //  'style' => 'width: 520px;',
              ],
              'format' => 'html',
              'value' => function($model){
                if( !empty($model->settings) ){
                  $table = '
                  <div style="width: 480px;">
                  <table class="table table-striped table-bordered tableWithoutBorders">';
                  foreach($model->settings as $item){

                    $shed_off = '_off';
                    $shed_isset = Schedule::find()->where(['site_id' => $item->id, 'disabled' => 0])->one();
                    if( $shed_isset ){
                      $shed_off = '';
                    }

                    $table .= '
                    <tr>
                      <td style="width: 75px">
                      '.$item->id.'
                      </td>
                      <td style="width: 55px">
                      '.$item->ch.'
                      </td>
                      <td style="width: 55px">
                      '.$item->d.'
                      </td>
                      <td style="width: 55px">
                      '.$item->oll.'
                      </td>
                      <td style="width: 85px">
                      '.$item->traf.'
                      </td>
                      <td style="width: 155px; white-space: nowrap;">
                      '.
                      (($item->view == 1)?

                      Html::a(Html::img('/images/admin/code.png', [
                        'style'=>'margin-right: 5px; width: 32px; height: 32px;'
                      ]), ['robot-edit', 'id'=>$item->id],
                      ['title' => 'Настройка шаблона', 'class' => 'blank']).
                      Html::a(Html::img('/images/admin/sched'.$shed_off.'.png', [
                        'style'=>'margin-right: 5px; width: 32px; height: 32px;'
                      ]), ['schedule', 'id'=>$item->id, 'user_id'=>$model->id],
                      ['title' => 'Расписание', 'class' => 'blank']).
                      Html::a(Html::img('/images/admin/settings.png', [
                        'style'=>'width: 32px; height: 32px;'
                      ]), ['updateset', 'id'=>$item->id],
                      ['title' => 'Редактировать', 'class' => 'blank']).
                      Html::a(Html::img('/images/admin/delete_id.png', [
                        'style'=>'width: 32px; height: 32px;'
                      ]), ['deleteset', 'id'=>$item->id, 'user_id'=>$model->id],
                      ['title' => 'Удалить', 'class' => 'confirm'])

                      :'<a href="'.Url::to(['archiveset', 'id' => $item->id, 'user_id'=>$model->id]).'" title="Восстановить настройку"><img src="/images/archive.png" align="absmiddle" />восстановить</a>')
                      .'
                      </td>
                    </tr>';
                  }
                  $table .= '</table>
                  <br />
                  <a class="robot-add" href="'.$model->id.'" style="color:#00b050"><i class="glyphicon glyphicon-plus"></i> Создать шаблон</a>
                  </div>';
                  return $table;
                }
                return '<div style="text-align: center">-</div>';
              }
            ],
            [
              'label' => 'Лимиты юзера (секунды)',
              'format' => 'html',
              'value' => function($model){
                $table = '<table class="table table-striped table-bordered tableWithoutBorders">';
                foreach($model->limits as $item){
                  $table .= '
                  <tr>
                    <td style="text-align: left">
                    '.$item["description"].'
                    </td>
                    <td>
                    '.($item["time"] ?? $item["default"]).'
                    </td>
                  </tr>';
                }
                $table .= '</table>';
                return $table;
              },
              'headerOptions' => [
              //  'style' => 'width: 340px;',
              ],
            ],
            [
              'class' => 'yii\grid\ActionColumn',
              'header' => 'Действия',
              'template' => '{delete}<br /> {update}<br /> {view}<br /> {login}<br /> {balans}',
              'headerOptions' => [
              //  'style' => 'width: 35px;',
              ],
              'contentOptions' => [
                'style' => 'text-align:center;',
              ],
              'buttons' => [
                'delete' => function ($url, $model, $key) {
                    return Html::a('[Удалить]', $url, [
                      'style' => 'color: #ff0000',
                      'class' => 'confirm',
                    ]);
                },
                'update' => function ($url, $model, $key) {
                    return Html::a('[Изменить]', $url, [
                      'style' => 'margin-top: 10px; display: inline-block',
                    ]);
                },
                'view' => function ($url, $model, $key) {
                    return Html::a('[Просмотр]', $url, [
                      'style' => 'margin-top: 10px; display: inline-block',
                    ]);
                },
                'login' => function ($url, $model, $key) {
                    return Html::a('[Войти]', $url, [
                      'class' => 'blank',
                      'style' => 'color: #00b050; margin-top: 10px; display: inline-block',
                    ]);
                },
                'balans' => function ($url, $model, $key) {
                    return Html::a('[Изменить баланс]', $url, [
                      'class' => 'blank',
                      'style' => 'color: #00b050; margin-top: 10px; display: inline-block',
                    ]);
                },
              ],
            ],
        ],
        'tableOptions' =>[
          'class' => 'table table-striped table-bordered',
        //  'style' => 'width: 1980px; max-width: none;',
        ]
    ]); ?>
</div>
