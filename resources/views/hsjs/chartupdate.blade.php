<script type="text/javascript">
    function chartSkin(chart, backgrounColor, borderRadius, borderColor, borderWidth) {
        chart.update({
            chart: {
                backgroundColor: backgrounColor,
                borderRadius: borderRadius,
                borderColor: borderColor,
                borderWidth: borderWidth,
            }
        });
    }

    function chartUpdate(chart, text, data) {
        chart.update({
            subtitle: {
                text: text
            },
            series: [{
                data: data
            }]
        });
    }
</script>
