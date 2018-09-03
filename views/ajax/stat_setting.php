<style>
  #container {
    min-width: 250px;
    max-width: 800px;
  }
  #ui-datepicker-div{
    z-index: 102 !important;
  }
</style>

<div id="container"></div>

<script>

//График после закрытия оставляет следы на странице, удаляем их
$('#myModalChart').one('reveal:close', function(){
  $('#myModalChart > p > *').remove();
});

var data = <?=json_encode($data)?>;

var buttonsForHours = [{
    type: 'hour',
    count: 12,
    text: '12ч'
}, {
    type: 'day',
    count: 1,
    text: '1д'
}, {
    type: 'day',
    count: 3,
    text: '3д'
}, {
    type: 'week',
    count: 1,
    text: '1н'
}, {
    type: 'all',
    text: 'Всё'
}];

var buttonsForDays = [{
    type: 'week',
    count: 2,
    text: '2н'
}, {
    type: 'month',
    count: 1,
    text: '1м'
}, {
    type: 'month',
    count: 3,
    text: '3м'
}, {
    type: 'month',
    count: 6,
    text: '6м'
}, {
    type: 'all',
    text: 'Всё'
}];

Highcharts.setOptions({
      lang: {
          loading: 'Загрузка...',
          months: ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'],
          weekdays: ['Воскресенье', 'Понедельник', 'Вторник', 'Среда', 'Четверг', 'Пятница', 'Суббота'],
          shortMonths: ['Янв', 'Фев', 'Март', 'Апр', 'Май', 'Июнь', 'Июль', 'Авг', 'Сент', 'Окт', 'Нояб', 'Дек'],
          exportButtonTitle: "Экспорт",
          printButtonTitle: "Печать",
          rangeSelectorFrom: "С",
          rangeSelectorTo: "По",
          rangeSelectorZoom: "Период",
          downloadPNG: 'Скачать PNG',
          downloadJPEG: 'Скачать JPEG',
          downloadPDF: 'Скачать PDF',
          downloadSVG: 'Скачать SVG',
          printChart: 'Напечатать график'
      }
});

$(function(){
  // Create the chart
  var chart = Highcharts.stockChart('container', {

      chart: {
          height: 400
      },

      title: {
          /*text: '<?=(($type == 1)? 'График количества показов за 2 недели':'График количества показов за полгода')?>'*/
      },

      colors: ['#00bc9c'],

      rangeSelector: {
          selected: 1,
          buttons: <?=(($type == 1)? 'buttonsForHours':'buttonsForDays')?>
      },

      series: [{
          name: 'Кол-во показов',
          data: data,
          type: 'area',
          threshold: null,
          tooltip: {
              valueDecimals: 0
          }
      }],

      responsive: {
          rules: [{
              condition: {
                  maxWidth: 500
              },
              chartOptions: {
                  chart: {
                      height: 300
                  },
                  subtitle: {
                      text: null
                  },
                  navigator: {
                      enabled: true
                  }
              }
          }]
      },

      navigation: {
          buttonOptions: {
              enabled: true
          }
      }
  });
  chart.setSize(null);

  $( ".highcharts-range-selector" ).datepicker({
    dateFormat: "yy-mm-dd"
  });

  $('text.highcharts-credits').hide();
});
</script>
