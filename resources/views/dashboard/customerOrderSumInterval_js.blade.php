<script type="text/javascript">
    $(function () {

        var coi = <?php echo App\Classes\dashboardClass::CustomerOrderSumInterval(date('Y-m-d', strtotime('today - 12 months')), date('Y-m-d', strtotime('today'))); ?>;

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
                cdata.push(parseInt(data[i].osszeg));
            }
            chartdata.push({name: mi, data: cdata});
            return chartdata;
        }

        var chart_customerOrderSumInterval = highchartLine( 'customerOrderSumInterval', 'line', 320, LineChartKategoria(coi), LineChartData(coi, ''),
            <?php echo "'" . langClass::trans('Megrendelés darab az elmúlt 12 hónapban') . "'"; ?>,
            <?php echo "'" . langClass::trans('havi bontás') . "'"; ?>,
            <?php echo "'" . langClass::trans('darab') . "'"; ?>);

    });

</script>
