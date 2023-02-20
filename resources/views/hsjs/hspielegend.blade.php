<script type="text/javascript">
function HighChartPie( renderTo, type, height, kategoria, data_view, chartTitleText, chartSubtitleText, seriesName, pieSize, datalabels, legend, innersize){
    var chart = Highcharts.chart( renderTo,{
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
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false,
            height: height,
            type: type,
            options3d: {
              enabled: true,
              alpha: 45,
              beta: 0
            }
        },
        title: {
            text: chartTitleText,
            style:{
              font: 'Palatino, URW Palladio L, serif',
              color: 'black',
              fontSize: '15px'
            }
        },
        subtitle: {
            text: chartSubtitleText,
            style:{
              font: 'Palatino, URW Palladio L, serif',
              color: 'black',
              fontSize: '12px'
            }
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
        },
        accessibility: {
            point: {
              valueSuffix: '%'
            }
        },
        plotOptions: {
            pie: {
                size: pieSize,
                allowPointSelect: true,
                cursor: 'pointer',
                depth: 35,
                dataLabels: {
                    enabled: datalabels,
                    format: '<b>{point.name}</b>: {point.percentage:.1f} %'
                },
                showInLegend: legend
            }
        },
        series: [{
            name: seriesName,
            innerSize: innersize,
            colorByPoint: true,
            data: data_view
            }]
    });
    return chart;
}

</script>
