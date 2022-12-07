<script type="text/javascript">
function HighChartColumn(
    renderTo, type, kategoria, data, height, borderRadius, borderColor, borderWidth, titleText, subtitleText, valueSuffix, inverted, polar) {
    var chart = Highcharts.chart({
        chart: {
            renderTo: renderTo,
            height: height,
            backgroundColor: '#EDE6E6',
            borderRadius: borderRadius,
            borderColor: borderColor,
            borderWidth: borderWidth,
            inverted: inverted,
            polar: polar,
            type: type
        },
        lang: {
          loading: 'Betöltés...',
          viewFullscreen: 'Teljes képernyő',
          exitFullscreen: 'Kilépés a teljes képernyőből',
          months: ['január', 'február', 'március', 'április', 'május','június',
                  'július', 'augusztus', 'szeptember', 'október', 'november', 'december'],
          shortMonths:  ['jan', 'febr', 'márc', 'ápr', 'máj', 'jún', 'júl', 'aug', 'szept', 'okt', 'nov', 'dec'],
          weekdays: ['vasárnap', 'hétfő', 'kedd', 'szerda', 'csütörtök', 'péntek', 'szombat'],
          exportButtonTitle: "Exportál",
          printButtonTitle: "Importál",
          rangeSelectorFrom: "ettől",
          rangeSelectorTo: "eddig",
          rangeSelectorZoom: "mutat:",
          downloadCSV: 'Letöltés CSV fileként',
          downloadXLS: 'Letöltés XLS fileként',
          downloadPNG: 'Letöltés PNG képként',
          downloadJPEG: 'Letöltés JPEG képként',
          downloadPDF: 'Letöltés PDF dokumentumként',
          downloadSVG: 'Letöltés SVG formátumban',
          resetZoom: "Visszaállít",
          resetZoomTitle: "Visszaállít",
          thousandsSep: "",
          decimalPoint: ',',
          viewData: 'Táblázat',
          printChart: 'Nyomtatás'
        },
      title: {
        text: titleText
      },

      subtitle: {
        text: subtitleText
      },

      xAxis: {
        categories: kategoria
      },
      tooltip: {
          split: false,
          valueSuffix: valueSuffix
      },
      series: data

    });
    return chart;
}
</script>
