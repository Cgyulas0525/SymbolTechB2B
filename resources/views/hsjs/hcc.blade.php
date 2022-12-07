<script type="text/javascript">
    function HCCFeltolt(data_view_at, renderTo, type, height, borderRadius, borderColor, borderWidth, titleText, subtitleText, valueSuffix, inverted, polar){
        var data_at = [];
        var kategoria_at = [];
        for (i=0;i<data_view_at.length; i++) {
            kategoria_at.push(data_view_at[i].nev);
            data_at.push(parseInt(data_view_at[i].darab));
        }
        console.log(data_at);
        var chart = HighChartColumn(renderTo, type, kategoria_at, data_at, height, borderRadius, borderColor, borderWidth, titleText, subtitleText, valueSuffix, inverted, polar);
        chartSkin(chart, '#FFFFFF', 25, 'lightgray', 3);
        return chart;
    }

    function HCCEgyszeru(paMi, chart){
        if (paMi != null){
            $(paMi).click(function () {
                combinatedClick( chart, false, false, 'Egyszerű');
            });
        }
    }

    function HCCInverz(paMi, chart){
        if (paMi != null){
            $(paMi).click(function () {
                combinatedClick( chart, true, false, 'Inverz');
            });
       }
    }

    function HCCPolar(paMi, chart){
        if (paMi != null){
            $(paMi).click(function () {
                combinatedClick( chart, false, true, 'Poláris');
            });
        }
    }

    function HCCGomb(plain, inverted, polar, chart){
        HCCEgyszeru(plain, chart);
        HCCInverz(inverted, chart);
        HCCPolar(polar, chart);
    }

    function HCCSmall(paMi, chart){
        if (paMi != null){
            document.getElementById(paMi).addEventListener('click', function () {
              chart.setSize(200);
            });
        }
    }

    function HCCBig(paMi, chart){
        if (paMi != null){
            document.getElementById(paMi).addEventListener('click', function () {
              chart.setSize(null);
            });
        }
    }

    function HCCSmallBigButton(small, big, chart){
        HCCSmall(small, chart);
        HCCBig(big, chart);
    }

    function HCCPieData(data, meddig){
        var pieData = [];
        var sum = 0;
        for (i = 0; i < data.length; i++){
            sum = 0;
            for (j = 0; j < meddig; j++){
                sum = sum + data[i].data[j];
            }
            pieData.push({name: data[i].name, y: sum});
        }
        return pieData;
    }

    function HCCPieDataSum(data){
        var pieData = [];
        for (i = 0; i < data.length; i++){
            pieData.push({name: data[i].tipus, y: parseInt(Math.round(data[i].osszeg).toFixed(0))});
        }
        return pieData;
    }
</script>
