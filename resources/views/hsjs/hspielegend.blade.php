<script type="text/javascript">
function HighChartPie( renderTo, type, height, kategoria, data_view, chartTitleText, chartSubtitleText, seriesName, pieSize, datalabels, legend, innersize){
    var chart = Highcharts.chart( renderTo,{
        lang: {
            loading: <?php echo "'" . App\Classes\langClass::trans('Betöltés...') . "'"; ?>,
            viewFullscreen: <?php echo "'" . App\Classes\langClass::trans('Teljes képernyő') . "'"; ?>,
            exitFullscreen: <?php echo "'" . App\Classes\langClass::trans('Kilépés a teljes képernyőből') . "'"; ?>,
            months: [ <?php echo "'" . App\Classes\langClass::trans('január') . "'"; ?>,
                <?php echo "'" . App\Classes\langClass::trans('február') . "'"; ?>,
                <?php echo "'" . App\Classes\langClass::trans('március') . "'"; ?>,
                <?php echo "'" . App\Classes\langClass::trans('április') . "'"; ?>,
                <?php echo "'" . App\Classes\langClass::trans('május') . "'"; ?>,
                <?php echo "'" . App\Classes\langClass::trans('június') . "'"; ?>,
                <?php echo "'" . App\Classes\langClass::trans('július') . "'"; ?>,
                <?php echo "'" . App\Classes\langClass::trans('augusztus') . "'"; ?>,
                <?php echo "'" . App\Classes\langClass::trans('szeptember') . "'"; ?>,
                <?php echo "'" . App\Classes\langClass::trans('október') . "'"; ?>,
                <?php echo "'" . App\Classes\langClass::trans('november') . "'"; ?>,
                <?php echo "'" . App\Classes\langClass::trans('december') . "'"; ?>],
            shortMonths:  [
                <?php echo "'" . App\Classes\langClass::trans('jan') . "'"; ?>,
                <?php echo "'" . App\Classes\langClass::trans('febr') . "'"; ?>,
                <?php echo "'" . App\Classes\langClass::trans('márc') . "'"; ?>,
                <?php echo "'" . App\Classes\langClass::trans('ápr') . "'"; ?>,
                <?php echo "'" . App\Classes\langClass::trans('máj') . "'"; ?>,
                <?php echo "'" . App\Classes\langClass::trans('jún') . "'"; ?>,
                <?php echo "'" . App\Classes\langClass::trans('júl') . "'"; ?>,
                <?php echo "'" . App\Classes\langClass::trans('aug') . "'"; ?>,
                <?php echo "'" . App\Classes\langClass::trans('szept') . "'"; ?>,
                <?php echo "'" . App\Classes\langClass::trans('okt') . "'"; ?>,
                <?php echo "'" . App\Classes\langClass::trans('nov') . "'"; ?>,
                <?php echo "'" . App\Classes\langClass::trans('dec') . "'"; ?>],
            weekdays: [
                <?php echo "'" . App\Classes\langClass::trans('vasárnap') . "'"; ?>,
                <?php echo "'" . App\Classes\langClass::trans('hétfő') . "'"; ?>,
                <?php echo "'" . App\Classes\langClass::trans('kedd') . "'"; ?>,
                <?php echo "'" . App\Classes\langClass::trans('szerda') . "'"; ?>,
                <?php echo "'" . App\Classes\langClass::trans('csütörtök') . "'"; ?>,
                <?php echo "'" . App\Classes\langClass::trans('péntek') . "'"; ?>,
                <?php echo "'" . App\Classes\langClass::trans('szombat') . "'"; ?>],
            exportButtonTitle: <?php echo "'" . App\Classes\langClass::trans("Exportál") . "'"; ?>,
            printButtonTitle: <?php echo "'" . App\Classes\langClass::trans("Importál") . "'"; ?>,
            rangeSelectorFrom: <?php echo "'" . App\Classes\langClass::trans("ettől") . "'"; ?>,
            rangeSelectorTo: <?php echo "'" . App\Classes\langClass::trans("eddig") . "'"; ?>,
            rangeSelectorZoom: <?php echo "'" . App\Classes\langClass::trans("mutat:") . "'"; ?>,
            downloadCSV: <?php echo "'" . App\Classes\langClass::trans('Letöltés CSV fileként') . "'"; ?>,
            downloadXLS: <?php echo "'" . App\Classes\langClass::trans('Letöltés XLS fileként') . "'"; ?>,
            downloadPNG: <?php echo "'" . App\Classes\langClass::trans('Letöltés PNG képként') . "'"; ?>,
            downloadJPEG: <?php echo "'" . App\Classes\langClass::trans('Letöltés JPEG képként') . "'"; ?>,
            downloadPDF: <?php echo "'" . App\Classes\langClass::trans('Letöltés PDF dokumentumként') . "'"; ?>,
            downloadSVG: <?php echo "'" . App\Classes\langClass::trans('Letöltés SVG formátumban') . "'"; ?>,
            resetZoom: <?php echo "'" . App\Classes\langClass::trans("Visszaállít") . "'"; ?>,
            resetZoomTitle: <?php echo "'" . App\Classes\langClass::trans("Visszaállít") . "'"; ?>,
            thousandsSep: "",
            decimalPoint: ',',
            viewData: <?php echo "'" . App\Classes\langClass::trans('Táblázat') . "'"; ?>,
            printChart: <?php echo "'" . App\Classes\langClass::trans('Nyomtatás') . "'"; ?>
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
