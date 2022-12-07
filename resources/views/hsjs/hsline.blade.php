<script type="text/javascript">
function highchartLine( renderTo, type, height, kategoria, data_view, chartTitleText, chartSubtitleText, yAxisTitleText){
    var chart = Highcharts.chart({
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
            categories: kategoria
        },
        yAxis: {
            title: {
              text: yAxisTitleText
            }
        },
        plotOptions: {
            line: {
              dataLabels: {
                enabled: true
              },
              enableMouseTracking: false
            }
        },
        series: data_view
    });
    return chart;
}

</script>
