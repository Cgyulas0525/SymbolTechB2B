<script type="text/javascript">
    function HCCAreaData(data, renderTo, type, height, chartTitleText, chartSubtitleText, yAxisTitleText, tooltipValueSuffix){
        var kategoria = kategoriafeltolt(data);
        var tipustomb = tipusfeltolt(data);
        var data_view = osszegfeltolt(tipustomb, kategoria, data);
        var chart = highchart( renderTo, type, height, kategoria, data_view, chartTitleText, chartSubtitleText, yAxisTitleText, tooltipValueSuffix);
        return chart;
    }

    function HCCPieDataElokeszit(data){
        var kategoria = kategoriafeltolt(data);
        var tipustomb = tipusfeltolt(data);
        var data_view = osszegfeltolt(tipustomb, kategoria, data);
        return data_view;
    }

    function PieData(data){
        var pie_data = [];
        for (i = 0; i < data.length; i++){
            pie_data.push({name: data[i].nev, y: parseInt(data[i].darab)});
        }
        return pie_data;
    }
</script>
