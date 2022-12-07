<script type="text/javascript">
    $(function () {

        var coi = <?php echo App\Classes\dashboardClass::CustomerOrderAverageSumInterval(date('Y-m-d', strtotime('today - 12 months')), date('Y-m-d', strtotime('today'))); ?>;

        function LineChartKategoria(data){
            kategoria = [];
            for (i = 0; i < data.length; i++){
                kategoria.push(data[i].nev);
            }
            return kategoria;
        }

        function LineChartData(data, mi){
            chartdata = [];
            cdata = [];
            for (i = 0; i < data.length; i++){
                cdata.push(parseInt((data[i].osszeg / data[i].darab).toFixed(0)));
            }
            chartdata.push({name: mi, data: cdata});
            return chartdata;
        }

        var chart_customerOrderAverageInterval = highchartLine( 'customerOrderAverageInterval', 'line', 320, LineChartKategoria(coi), LineChartData(coi, ''),
            <?php echo "'" . App\Classes\langClass::trans('Megrendelés átlag értékek az elmúlt 12 hónapban') . "'"; ?>,
            <?php echo "'" . App\Classes\langClass::trans('havi bontás') . "'"; ?>,
            <?php echo "'" . App\Classes\langClass::trans('forint') . "'"; ?>);

    });

</script>
