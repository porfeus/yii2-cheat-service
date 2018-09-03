<?php

use app\widgets\Alert;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use app\models\Conf;
?>

<center>
   <?= Html::beginForm(); ?>
   <?php
      $echo = [];
      foreach ($myids as $row){
        $echo[] = '<b>'.Html::encode($row['id']).'</b><input type="hidden" class="Cids" name="ids[]" value="'.Html::encode($row['id']).'" />';
      }
      ?>
   <?php
      echo implode(', ', $echo);
      ?>
   <hr />
   <table width="80%">
      <tr>
         <td>Пополнить реалами (шт):</td>
         <td><input type="text" id="views_up" size="20"/> <input onclick="views_op('views_up', this);" type="button" value="Выполнить" /></td>
      </tr>
      <tr>
         <td>Снять реалы (шт):</td>
         <td><input type="text" id="views_dn" size="20"/> <input onclick="views_op('views_dn', this);" type="button" value="Выполнить" /></td>
      </tr>
      <tr>
         <td>Снять доступные реалы:</td>
         <td><input type="text" style="color: #777" value="Все доступные реалы" readonly="readonly" id="views_dn_all" size="20" />
		 <input onclick="views_op('views_dn_all', this);" type="button" value="Выполнить" /></td>
      </tr>
      <p>Вы можете пополнить реалами или снять их сразу у нескольких настроек сразу, для этого просто выберите нужные настройки и произведите требуемую операцию. Также есть возможность снять все имеющиеся реалы на выбранных настройках.</p>
   </table>
   <script>
      function views_op(op, el) {
          $views = $("#"+op).val();
          if (!$views) {
              alert ("Введите количество реалов");
              return false;
          }
          else {
              $("input[type='button']").prop("disabled", true);
              var all = 0;
              if (op == "views_dn_all") {
                  op = "views_dn";
                  all = 1;
              }

              var ids = "";

              $(".Cids").each(function() {
                  ids += $(this).val()+",";
              });

              $.post ("/ajax",{
                  "do": "cred_op",
                  "op": op,
                  "all": all,
                  "ids": ids,
                  "views": $views
              },
              function (data)
              {
                  $("input[type='button']").prop("disabled", false);
                  if (data == 1) {
                      $("#"+op).val("");
                      alert ("Выполнено!");
                      document.location.reload();
                  }
                  else {
                      alert (data);
                      if( data.indexOf('Успешно') > -1 ){
                        document.location.reload();
                      }
                  }

              });
          }
      }
   </script>
   <?= Html::endForm(); ?>
</center>
