<script type="text/javascript">
    function combinatedClick(chart, inverted, polar, text) {
        chart.update({
            chart: {
                inverted: inverted,
                polar: polar
            },
            subtitle: {
                text: text
            }
        });
    }
</script>
