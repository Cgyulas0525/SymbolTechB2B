<script type="text/javascript">
function highchart( renderTo, type, height, kategoria, data_view, chartTitleText, chartSubtitleText, yAxisTitleText, tooltipValueSuffix){
    var chart = new Highcharts.chart({
        lang: {
          loading: <?php echo "'" . langClass::trans('Betöltés...') . "'"; ?>,
          viewFullscreen: <?php echo "'" . langClass::trans('Teljes képernyő') . "'"; ?>,
          exitFullscreen: <?php echo "'" . langClass::trans('Kilépés a teljes képernyőből') . "'"; ?>,
          months: [ <?php echo "'" . langClass::trans('január') . "'"; ?>,
                    <?php echo "'" . langClass::trans('február') . "'"; ?>,
                    <?php echo "'" . langClass::trans('március') . "'"; ?>,
                    <?php echo "'" . langClass::trans('április') . "'"; ?>,
                    <?php echo "'" . langClass::trans('május') . "'"; ?>,
                    <?php echo "'" . langClass::trans('június') . "'"; ?>,
                    <?php echo "'" . langClass::trans('július') . "'"; ?>,
                    <?php echo "'" . langClass::trans('augusztus') . "'"; ?>,
                    <?php echo "'" . langClass::trans('szeptember') . "'"; ?>,
                    <?php echo "'" . langClass::trans('október') . "'"; ?>,
                    <?php echo "'" . langClass::trans('november') . "'"; ?>,
                    <?php echo "'" . langClass::trans('december') . "'"; ?>],
          shortMonths:  [
              <?php echo "'" . langClass::trans('jan') . "'"; ?>,
              <?php echo "'" . langClass::trans('febr') . "'"; ?>,
              <?php echo "'" . langClass::trans('márc') . "'"; ?>,
              <?php echo "'" . langClass::trans('ápr') . "'"; ?>,
              <?php echo "'" . langClass::trans('máj') . "'"; ?>,
              <?php echo "'" . langClass::trans('jún') . "'"; ?>,
              <?php echo "'" . langClass::trans('júl') . "'"; ?>,
              <?php echo "'" . langClass::trans('aug') . "'"; ?>,
              <?php echo "'" . langClass::trans('szept') . "'"; ?>,
              <?php echo "'" . langClass::trans('okt') . "'"; ?>,
              <?php echo "'" . langClass::trans('nov') . "'"; ?>,
              <?php echo "'" . langClass::trans('dec') . "'"; ?>],
          weekdays: [
              <?php echo "'" . langClass::trans('vasárnap') . "'"; ?>,
              <?php echo "'" . langClass::trans('hétfő') . "'"; ?>,
              <?php echo "'" . langClass::trans('kedd') . "'"; ?>,
              <?php echo "'" . langClass::trans('szerda') . "'"; ?>,
              <?php echo "'" . langClass::trans('csütörtök') . "'"; ?>,
              <?php echo "'" . langClass::trans('péntek') . "'"; ?>,
              <?php echo "'" . langClass::trans('szombat') . "'"; ?>],
          exportButtonTitle: <?php echo "'" . langClass::trans("Exportál") . "'"; ?>,
          printButtonTitle: <?php echo "'" . langClass::trans("Importál") . "'"; ?>,
          rangeSelectorFrom: <?php echo "'" . langClass::trans("ettől") . "'"; ?>,
          rangeSelectorTo: <?php echo "'" . langClass::trans("eddig") . "'"; ?>,
          rangeSelectorZoom: <?php echo "'" . langClass::trans("mutat:") . "'"; ?>,
          downloadCSV: <?php echo "'" . langClass::trans('Letöltés CSV fileként') . "'"; ?>,
          downloadXLS: <?php echo "'" . langClass::trans('Letöltés XLS fileként') . "'"; ?>,
          downloadPNG: <?php echo "'" . langClass::trans('Letöltés PNG képként') . "'"; ?>,
          downloadJPEG: <?php echo "'" . langClass::trans('Letöltés JPEG képként') . "'"; ?>,
          downloadPDF: <?php echo "'" . langClass::trans('Letöltés PDF dokumentumként') . "'"; ?>,
          downloadSVG: <?php echo "'" . langClass::trans('Letöltés SVG formátumban') . "'"; ?>,
          resetZoom: <?php echo "'" . langClass::trans("Visszaállít") . "'"; ?>,
          resetZoomTitle: <?php echo "'" . langClass::trans("Visszaállít") . "'"; ?>,
          thousandsSep: "",
          decimalPoint: ',',
          viewData: <?php echo "'" . langClass::trans('Táblázat') . "'"; ?>,
          printChart: <?php echo "'" . langClass::trans('Nyomtatás') . "'"; ?>
        },
        chart: {
            renderTo: renderTo,
            height: height,
            type: type
        },
        title: {
            text: chartTitleText
        },
        subtitle: {
            text: chartSubtitleText
        },
        xAxis: {
            categories: kategoria,
            tickmarkPlacement: 'on',
            title: {
                enabled: false
            }
        },
        yAxis: {
            title: {
                text: yAxisTitleText
            },
            labels: {
                formatter: function () {
                    return this.value;
              }
            }
        },
        tooltip: {
            split: true,
            valueSuffix: tooltipValueSuffix
        },
        plotOptions: {
            area: {
                stacking: 'normal',
                lineColor: '#666666',
                lineWidth: 1,
                marker: {
                    lineWidth: 1,
                    lineColor: '#666666'
                }
            }
        },
        series: data_view
    });
    return chart;
}
</script>
